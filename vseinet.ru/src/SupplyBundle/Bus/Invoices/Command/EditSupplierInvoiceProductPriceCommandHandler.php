<?php 

namespace SupplyBundle\Bus\Invoices\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\OperationTypeCode;
use Doctrine\ORM\Query\ResultSetMapping;
use ServiceBundle\Components\Number;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EditSupplierInvoiceProductPriceCommandHandler extends MessageHandler
{
    public function handle(EditSupplierInvoiceProductPriceCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var \AppBundle\Entity\User $user
         */
        $user = $this->get('user.identity')->getUser();

        if ($command->purchasePrice <= 0) {
            throw new BadRequestHttpException('Недопустимое значение цены');
        }
        if ($command->newPurchasePrice <= 0) {
            throw new BadRequestHttpException('Недопустимое значение новой цены');
        }

        $supplierReserveIdSql = empty($command->supplierReserveId) ? 'IS NULL' : ' = '.$command->supplierReserveId;

        if ($command->newPurchasePrice != $command->purchasePrice) {
            $sql = '
                INSERT INTO supplier_reserve_register ( base_product_id, delta, order_item_id, supplier_reserve_id, supply_id, purchase_price, supplier_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                (
                SELECT
                    srr.base_product_id,
                    SUM( - srr.delta ),
                    srr.order_item_id,
                    sr.id,
                    s.id,
                    srr.purchase_price,
                    srr.supplier_id,
                    sr.id AS registrator_id,
                    :supplier_reserve :: document_type_code,
                    sr.created_at AS registered_at,
                    :supply_item_change :: operation_type_code,
                    now( ),
                    :user_id::INTEGER 
                FROM
                    supplier_reserve_register AS srr
                    JOIN supply AS s ON s.id = srr.supply_id
                    LEFT JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id 
                WHERE
                    s.id = :supply_id 
                    AND srr.order_item_id = :order_item_id 
                    AND srr.purchase_price = :purchase_price 
                    AND srr.supplier_reserve_id '.$supplierReserveIdSql.' 
                GROUP BY
                    srr.base_product_id,
                    srr.order_item_id,
                    sr.id,
                    srr.purchase_price,
                    s.id,
                    srr.supplier_id 
                HAVING
                    SUM( delta ) > 0 
                
                UNION ALL
                
                SELECT
                    srr.base_product_id,
                    SUM( srr.delta ),
                    srr.order_item_id,
                    sr.id,
                    s.id,
                    :new_purchase_price,
                    srr.supplier_id,
                    sr.id,
                    :supplier_reserve,
                    sr.created_at,
                    :supply_item_change :: operation_type_code,
                    now( ),
                    :user_id::INTEGER 
                FROM
                    supplier_reserve_register AS srr
                    JOIN supply AS s ON s.id = srr.supply_id
                    JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id 
                WHERE
                    srr.supply_id = :supply_id 
                    AND srr.order_item_id = :order_item_id 
                    AND srr.purchase_price = :purchase_price 
                    AND srr.supplier_reserve_id '.$supplierReserveIdSql.' 
                GROUP BY
                    srr.base_product_id,
                    srr.order_item_id,
                    srr.supplier_reserve_id,
                    srr.purchase_price,
                    sr.id,
                    s.id,
                    srr.supplier_id 
                HAVING
                    SUM( delta ) > 0 
                )
            ';
            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
            $statement->bindValue('supply_item_change', OperationTypeCode::SUPPLY_ITEM_CHANGE);
            $statement->bindValue('user_id', $user->getId());
            $statement->bindValue('supply_id', $command->id);
            $statement->bindValue('order_item_id', $command->orderItemId);
            $statement->bindValue('purchase_price', $command->purchasePrice);
            $statement->bindValue('new_purchase_price', $command->newPurchasePrice);
            $statement->bindValue('supplier_reserve_id', $command->supplierReserveId);
            $statement->execute();
        }
    }
}