<?php 

namespace SupplyBundle\Bus\Invoices\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\OperationTypeCode;
use SupplyBundle\Entity\ViewSupplierOrderItem;
use ReservesBundle\Entity\Shipment;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateSeparateSupplierInvoiceCommandHandler extends MessageHandler
{
    public function handle(CreateSeparateSupplierInvoiceCommand $command)
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
            INSERT INTO supply ( created_at, created_by, supplier_id, comment, supplier_counteragent_id, our_counteragent_id, destination_point_id ) 
            SELECT
                now( ),
                :user_id::INTEGER,
                supplier_id,
                comment,
                supplier_counteragent_id,
                our_counteragent_id,
                :point_id 
            FROM
                supply 
            WHERE
                id = :supply_id 
                AND registered_at IS NULL 
            RETURNING id
        ';
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('user_id', $currentUser->getId());
        $statement->bindValue('point_id', $command->geoPointId);
        $statement->bindValue('supply_id', $command->id);
        $statement->execute();

        $newSupplyId = $statement->fetchColumn();

        if (empty($newSupplyId)) {
            throw new NotFoundHttpException('Исходного счета не существует или он уже закрыт для редактирования');
        }

        $this->get('uuid.manager')->saveId($command->uuid, $newSupplyId);

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
                    COALESCE ( sr.id, ssr.id, sr2.id ) AS registrator_id,
                    :supplier_reserve :: document_type_code,
                    COALESCE ( sr.created_at, ssr.created_at, sr2.created_at ) AS registered_at,
                    :supply_forming :: operation_type_code,
                    now( ),
                    :user_id::INTEGER 
                FROM
                    supplier_reserve_register AS srr
                    JOIN supply AS s ON s.id = srr.supply_id
                    JOIN order_item AS oi ON oi.id = srr.order_item_id
                    JOIN "order" AS o ON o.id = oi.order_id
                    LEFT JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id
                    LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = srr.supplier_id 
                        AND ssr.closed_at IS NULL 
                        AND ssr.is_shipping = TRUE 
                    LEFT JOIN supplier_reserve AS sr2 ON sr2.supplier_id = srr.supplier_id 
                        AND sr2.closed_at IS NULL 
                        AND sr2.is_shipping = FALSE 
                WHERE
                    s.id = :supply_id 
                    AND o.geo_point_id = :point_id 
                GROUP BY
                    srr.base_product_id,
                    srr.order_item_id,
                    sr.id,
                    ssr.id,
                    sr2.id,
                    srr.purchase_price,
                    srr.supplier_id,
                    s.id 
                HAVING
                    SUM( delta ) > 0 
            ) 
            UNION ALL
            (
                SELECT
                    srr.base_product_id,
                    SUM( srr.delta ),
                    srr.order_item_id,
                    sr.id,
                    s.id,
                    srr.purchase_price,
                    srr.supplier_id,
                    COALESCE ( sr.id, ssr.id, sr2.id ) AS registrator_id,
                    :supplier_reserve :: document_type_code,
                    COALESCE ( sr.created_at, ssr.created_at, sr2.created_at ) AS registered_at,
                    :supply_forming :: operation_type_code,
                    now( ),
                    :user_id::INTEGER 
                FROM
                    supplier_reserve_register AS srr
                    JOIN supply AS s ON s.id = :new_supply_id
                    JOIN order_item AS oi ON oi.id = srr.order_item_id
                    JOIN "order" AS o ON o.id = oi.order_id
                    LEFT JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id
                    LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = srr.supplier_id 
                        AND ssr.closed_at IS NULL 
                        AND ssr.is_shipping = TRUE 
                    LEFT JOIN supplier_reserve AS sr2 ON sr2.supplier_id = srr.supplier_id 
                        AND sr2.closed_at IS NULL 
                        AND sr2.is_shipping = FALSE 
                WHERE
                    srr.supply_id = :supply_id 
                    AND o.geo_point_id = :point_id 
                GROUP BY
                    srr.base_product_id,
                    srr.order_item_id,
                    sr.id,
                    ssr.id,
                    sr2.id,
                    srr.purchase_price,
                    s.id,
                    srr.supplier_id 
                HAVING
                    SUM( delta ) > 0 
            )
        ';
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('new_supply_id', $newSupplyId);
        $statement->bindValue('supply_id', $command->id);
        $statement->bindValue('point_id', $command->geoPointId);
        $statement->bindValue('user_id', $currentUser->getId());
        $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
        $statement->bindValue('supply_forming', OperationTypeCode::SUPPLY_FORMING);
        $statement->execute();
    }
}