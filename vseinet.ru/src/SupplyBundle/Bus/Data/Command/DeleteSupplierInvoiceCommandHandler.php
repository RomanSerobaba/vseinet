<?php 

namespace SupplyBundle\Bus\Data\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\OperationTypeCode;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteSupplierInvoiceCommandHandler extends MessageHandler
{
    public function handle(DeleteSupplierInvoiceCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $sql = '
            DELETE FROM
                supply 
            WHERE
                id = :supply_id 
                AND registered_at IS NULL 
            RETURNING id
        ';
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('invoice_id', $command->id);
        $statement->execute();

        $id = $statement->fetchColumn();

        if ($id > 0) {
            // меняем регистр
            $sql = '
                INSERT INTO supplier_reserve_register ( base_product_id, delta, order_item_id, supplier_reserve_id, supply_id, purchase_price, supplier_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                (
                SELECT
                    srr.base_product_id,
                    SUM ( - srr.delta ),
                    srr.order_item_id,
                    sr.id,
                    srr.supply_id,
                    srr.purchase_price,
                    srr.supplier_id,
                    COALESCE ( sr.id, ssr.id, sr2.id ) AS registrator_id,
                    :supplier_reserve :: document_type_code,
                    COALESCE ( sr.created_at, ssr.created_at, sr2.created_at ) AS registered_at,
                    :supply_forming :: operation_type_code,
                    now(), 	
                    :user_id::INTEGER 
                FROM
                    supplier_reserve_register AS srr
                    JOIN order_item AS oi ON oi.id = srr.order_item_id
                    LEFT JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id
                    LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = srr.supplier_id 
                        AND ssr.closed_at IS NULL 
                        AND ssr.is_shipping = TRUE 
                    LEFT JOIN supplier_reserve AS sr2 ON sr2.supplier_id = srr.supplier_id 
                        AND sr2.closed_at IS NULL 
                        AND sr2.is_shipping = FALSE 
                WHERE
                    srr.supply_id = :supply_id 
                GROUP BY
                    srr.base_product_id,
                    srr.order_item_id,
                    sr.id,
                    ssr.id,
                    sr2.id,
                    srr.purchase_price,
                    srr.supplier_id,
                    srr.supply_id 
                HAVING
                    SUM ( delta ) > 0 
                ) 
                
                UNION ALL
                
                (
                SELECT
                    srr.base_product_id,
                    SUM ( srr.delta ),
                    srr.order_item_id,
                    sr.id,
                    NULL,
                    srr.purchase_price,
                    srr.supplier_id,
                    COALESCE ( sr.id, ssr.id, sr2.id ) AS registrator_id,
                    :supplier_reserve :: document_type_code,
                    COALESCE ( sr.created_at, ssr.created_at, sr2.created_at ) AS registered_at,
                    :supply_forming :: operation_type_code,
                    now(), 	
                    :user_id::INTEGER 
                FROM
                    supplier_reserve_register AS srr
                    JOIN order_item AS oi ON oi.id = srr.order_item_id
                    LEFT JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id
                    LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = srr.supplier_id 
                        AND ssr.closed_at IS NULL 
                        AND ssr.is_shipping = TRUE 
                    LEFT JOIN supplier_reserve AS sr2 ON sr2.supplier_id = srr.supplier_id 
                        AND sr2.closed_at IS NULL 
                        AND sr2.is_shipping = FALSE 
                WHERE
                    srr.supply_id = :supply_id 
                    AND srr.order_item_id > 0 
                GROUP BY
                    srr.base_product_id,
                    srr.order_item_id,
                    sr.id,
                    ssr.id,
                    sr2.id,
                    srr.purchase_price,
                    srr.supply_id,
                    srr.supplier_id 
                HAVING
                    SUM ( delta ) > 0 
                )
            ';
            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
            $statement->bindValue('supply_forming', OperationTypeCode::SUPPLY_FORMING);
            $statement->bindValue('user_id', $currentUser->getId());
            $statement->bindValue('supply_id', $command->id);
            $statement->execute();
        } else {
            throw new NotFoundHttpException('Невозможно удалить закрытый счет');
        }
    }
}