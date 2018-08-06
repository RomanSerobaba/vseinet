<?php 

namespace SupplyBundle\Bus\Invoices\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\OperationTypeCode;
use Doctrine\ORM\Query\ResultSetMapping;
use ServiceBundle\Components\Number;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DeletePositionFromSupplierInvoiceCommandHandler extends MessageHandler
{
    public function handle(DeletePositionFromSupplierInvoiceCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        // меняем счет
        $values = [];

        foreach ($command->items as $item) {
            $values[] = sprintf('(%u::INTEGER, %u::INTEGER, %s::INTEGER)', $item['orderItemId'], $item['purchasePrice'], $item['supplierReserveId'] ?? 'NULL');
        }

        if (!$values) {
            throw new BadRequestHttpException('Items are empty');
        }

        $sql = '
            WITH DATA ( order_item_id, purchase_price, supplier_reserve_id ) AS 
            ( VALUES '.implode(',', $values).' ) 
            INSERT INTO supplier_reserve_register ( base_product_id, delta, order_item_id, supplier_reserve_id, supply_id, purchase_price, supplier_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) (
            SELECT
                srr.base_product_id,
                SUM ( - srr.delta ),
                srr.order_item_id,
                sr.id,
                s.id,
                srr.purchase_price,
                srr.supplier_id,
                COALESCE ( sr.id, s.id ) AS registrator_id,
                :supplier_reserve :: document_type_code,
                COALESCE ( sr.created_at, ssr.created_at, sr2.created_at ) AS registered_at,
                :supply_item_deleting :: operation_type_code,
                now(),  
                :user_id::INTEGER 
            FROM
                supplier_reserve_register AS srr
                JOIN supply AS s ON s.id = srr.supply_id
                LEFT JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id
                JOIN DATA AS d ON d.order_item_id = srr.order_item_id 
                    AND d.purchase_price = srr.purchase_price 
                    AND CASE WHEN d.supplier_reserve_id IS NULL 
                        THEN srr.supplier_reserve_id IS NULL 
                        ELSE d.supplier_reserve_id = srr.supplier_reserve_id
                    END 
                LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = srr.supplier_id 
                    AND ssr.is_shipping = TRUE 
                    AND ssr.closed_at IS NULL 
                JOIN supplier_reserve AS sr2 ON sr2.supplier_id = srr.supplier_id 
                    AND sr2.is_shipping = FALSE 
                    AND sr2.closed_at IS NULL 
            WHERE
                s.id = :supply_id 
            GROUP BY
                srr.base_product_id,
                srr.order_item_id,
                sr.id,
                srr.purchase_price,
                s.id,
                srr.supplier_id,
                ssr.created_at,
                sr2.created_at
            HAVING SUM ( delta ) > 0 
                
            UNION ALL
            
            SELECT
                srr.base_product_id,
                SUM ( srr.delta ),
                srr.order_item_id,
                sr.id,
                NULL,
                srr.purchase_price,
                srr.supplier_id,
                sr.id,
                :supplier_reserve,
                COALESCE ( sr.created_at, ssr.created_at, sr2.created_at ),
                :supply_item_deleting :: operation_type_code,
                now(),  
                :user_id::INTEGER 
            FROM
                supplier_reserve_register AS srr
                JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id
                JOIN DATA AS d ON d.order_item_id = srr.order_item_id 
                    AND d.purchase_price = srr.purchase_price 
                    AND CASE WHEN d.supplier_reserve_id IS NULL 
                        THEN srr.supplier_reserve_id IS NULL 
                        ELSE d.supplier_reserve_id = srr.supplier_reserve_id
                    END 
                LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = srr.supplier_id 
                    AND ssr.is_shipping = TRUE 
                    AND ssr.closed_at IS NULL 
                JOIN supplier_reserve AS sr2 ON sr2.supplier_id = srr.supplier_id 
                    AND sr2.is_shipping = FALSE 
                    AND sr2.closed_at IS NULL 
            WHERE
                srr.supply_id = :supply_id 
            GROUP BY
                srr.base_product_id,
                srr.order_item_id,
                srr.supplier_reserve_id,
                srr.purchase_price,
                sr.id,
                srr.supplier_id,
                ssr.created_at,
                sr2.created_at 
            HAVING
                SUM ( delta ) > 0 
            )
        ';
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('user_id', $currentUser->getId());
        $statement->bindValue('supply_id', $command->id);
        $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
        $statement->bindValue('supply_item_deleting', OperationTypeCode::SUPPLIER_INVOICE_DELETING);
        $statement->execute();
    }
}