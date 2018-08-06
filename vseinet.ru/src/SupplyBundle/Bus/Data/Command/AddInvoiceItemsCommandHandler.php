<?php 

namespace SupplyBundle\Bus\Data\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\GoodsReserveOperationCode;
use AppBundle\Enum\GoodsReserveType;
use AppBundle\Enum\OperationTypeCode;
use http\Exception\BadQueryStringException;
use ServiceBundle\Components\Number;
use SupplyBundle\Entity\ViewSupply;

class AddInvoiceItemsCommandHandler extends MessageHandler
{
    public function handle(AddInvoiceItemsCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();
        
        $values = [];

        foreach ($command->items as $item) {
            $values[] = sprintf(
                '(%u::INTEGER, %u::INTEGER, '.(!empty($item['supplierReserveId']) ? sprintf('%u::INTEGER', $item['supplierReserveId']) : 'NULL::INTEGER').')',
                $item['orderItemId'],
                $item['purchasePrice']
            );
        }
        
        if (!$values) {
            return;
        }

        // Регистр резервов
        $sql = '
            WITH DATA ( order_item_id, purchase_price, supplier_reserve_id ) AS 
            ( VALUES '.implode(',', $values).' ) 
            INSERT INTO supplier_reserve_register ( base_product_id, delta, order_item_id, supplier_reserve_id, supply_id, purchase_price, supplier_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
            SELECT
                srr.base_product_id,
                SUM ( - srr.delta ),
                srr.order_item_id,
                sr.id,
                NULL,
                srr.purchase_price,
                srr.supplier_id,
                COALESCE ( sr.id, ssr.id, sr2.id ),
                :supplier_reserve :: document_type_code,
                COALESCE ( sr.created_at, ssr.created_at, sr2.created_at ),
                :supply_item_adding :: operation_type_code,
                now( ), 
                :user_id :: INTEGER 
            FROM
                supplier_reserve_register AS srr
                JOIN DATA AS d ON d.order_item_id = srr.order_item_id 
                    AND d.purchase_price = srr.purchase_price 
                    AND d.supplier_reserve_id = srr.supplier_reserve_id
                LEFT JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id
                JOIN supplier_reserve AS sr2 ON sr2.supplier_id = srr.supplier_reserve_id 
                    AND sr2.is_shipping = FALSE 
                    AND sr2.closed_at IS NULL 
                LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = sr.supplier_id 
                    AND ssr.is_shipping = TRUE 
                    AND ssr.closed_at IS NULL 
                JOIN supply AS s ON s.id = :supply_id 
            WHERE
                srr.supply_id IS NULL 
                AND srr.supplier_reserve_id > 0 
            GROUP BY
                srr.base_product_id,
                srr.order_item_id,
                srr.supplier_reserve_id,
                srr.purchase_price,
                sr.id,
                ssr.id,
                srr.supplier_id,
                sr2.id 
            HAVING
                SUM ( srr.delta ) > 0 
            
            UNION ALL
            
            SELECT
                srr.base_product_id,
                SUM ( srr.delta ),
                srr.order_item_id,
                sr.id,
                s.id,
                srr.purchase_price,
                srr.supplier_id,
                COALESCE ( sr.id, ssr.id, sr2.id ),
                :supplier_reserve :: document_type_code,
                COALESCE ( sr.created_at, ssr.created_at, sr2.created_at ),
                :supply_item_adding :: operation_type_code,
                now( ), 	
                :user_id :: INTEGER 
            FROM
                supplier_reserve_register AS srr
                JOIN DATA AS d ON d.order_item_id = srr.order_item_id 
                    AND d.purchase_price = srr.purchase_price 
                    AND d.supplier_reserve_id = srr.supplier_reserve_id
                LEFT JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id
                JOIN supplier_reserve AS sr2 ON sr2.supplier_id = srr.supplier_reserve_id 
                    AND sr2.is_shipping = FALSE 
                    AND sr2.closed_at IS NULL 
                LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = sr.supplier_id 
                    AND ssr.is_shipping = TRUE 
                    AND ssr.closed_at IS NULL 
                JOIN supply AS s ON s.id = :supply_id 
            WHERE
                srr.supply_id IS NULL 
                AND srr.supplier_reserve_id > 0 
            GROUP BY
                srr.base_product_id,
                srr.order_item_id,
                srr.supplier_reserve_id,
                srr.purchase_price,
                sr.id,
                ssr.id,
                srr.supplier_id,
                sr2.id,
                s.id 
            HAVING
                SUM ( srr.delta ) > 0 
            
            UNION ALL
            
            SELECT
                gnr.base_product_id,
                gnr.delta AS delta,
                gnr.order_item_id,
                d.supplier_reserve_id,
                s.id AS supply_id,
                d.purchase_price,
                s.supplier_id,
                COALESCE ( ssr.id, sr.id ) AS registrator_id,
                :supplier_reserve :: document_type_code AS registrator_type_code,
                COALESCE ( ssr.created_at, sr.created_at ) AS registered_at,
                :supply_item_adding :: operation_type_code AS register_operation_type_code,
                now( ) AS created_at, 	
                :user_id :: INTEGER AS created_by 
            FROM
                DATA AS d
                JOIN get_goods_need_register_data ( CURRENT_TIMESTAMP :: TIMESTAMP, NULL, d.order_item_id ) AS gnr ON 1 = 1
                JOIN supply AS s ON s.id = :supply_id
                JOIN supplier_reserve AS sr ON sr.supplier_id = s.supplier_id 
                    AND sr.is_shipping = FALSE 
                    AND sr.closed_at IS NULL 
                LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = s.supplier_id 
                    AND ssr.is_shipping = TRUE 
                    AND ssr.closed_at IS NULL 
            WHERE
                d.supplier_reserve_id IS NULL 
                AND gnr.delta > 0
        ';

        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('supply_id', $command->id);
        $statement->bindValue('user_id', $currentUser->getId());
        $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
        $statement->bindValue('supply_item_adding', OperationTypeCode::SUPPLY_ITEM_ADDING);
        $statement->execute();

        // Регистр потребностей
        $sql = '
            WITH DATA ( order_item_id, purchase_price, supplier_reserve_id ) AS 
            ( VALUES '.implode(',', $values).' ) 
            INSERT INTO goods_need_register ( base_product_id, delta, order_item_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
            SELECT
                gnr.base_product_id, - gnr.delta AS delta,
                gnr.order_item_id,
                COALESCE ( ssr.id, sr.id ) AS registrator_id,
                :supplier_reserve :: document_type_code AS registrator_type_code,
                COALESCE ( ssr.created_at, sr.created_at ) AS registered_at,
                :supply_item_adding :: operation_type_code AS register_operation_type_code,
                now( ) AS created_at, 
                :user_id :: INTEGER AS created_by 
            FROM
                DATA AS d
                JOIN get_goods_need_register_data ( CURRENT_TIMESTAMP :: TIMESTAMP, NULL, d.order_item_id ) AS gnr ON 1 = 1
                JOIN supply AS s ON s.id = :supply_id
                JOIN supplier_reserve AS sr ON sr.supplier_id = s.supplier_id 
                    AND sr.is_shipping = FALSE 
                    AND sr.closed_at IS NULL 
                LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = s.supplier_id 
                    AND ssr.is_shipping = TRUE 
                    AND ssr.closed_at IS NULL 
            WHERE
                d.supplier_reserve_id IS NULL 
                AND gnr.delta > 0
        ';

        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('supply_id', $command->id);
        $statement->bindValue('user_id', $currentUser->getId());
        $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
        $statement->bindValue('supply_item_adding', OperationTypeCode::SUPPLY_ITEM_ADDING);
        $statement->execute();
   }
}