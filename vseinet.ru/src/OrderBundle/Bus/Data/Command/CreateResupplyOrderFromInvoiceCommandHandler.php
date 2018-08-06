<?php 

namespace OrderBundle\Bus\Data\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\GoodsNeedRegisterType;
use AppBundle\Enum\OperationTypeCode;
use AppBundle\Enum\OrderTypeCode;
use ServiceBundle\Components\Number;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateResupplyOrderFromInvoiceCommandHandler extends MessageHandler
{
    public function handle(CreateResupplyOrderFromInvoiceCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        if ($command->purchasePrice <= 0) {
            throw new BadRequestHttpException('Недопустимое значение цены');
        }

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $em->getConnection()->beginTransaction();
        try
        {
            $sql = '
                INSERT INTO "order" ( created_at, created_by, manager_id, our_seller_counteragent_id, geo_point_id, type_code, geo_city_id, registered_at, registered_by ) 
                SELECT
                    now( ),
                    :user_id::INTEGER,
                    :user_id::INTEGER,
                    s.our_counteragent_id,
                    s.destination_point_id,
                    :resupply,
                    gp.geo_city_id,
                    now(), 
                    :user_id::INTEGER
                FROM
                    supply AS s
                    JOIN geo_point AS gp ON gp.id = s.destination_point_id 
                WHERE
                    s.id = :supply_id 
                RETURNING id
            ';
            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('user_id', $currentUser->getId());
            $statement->bindValue('resupply', OrderTypeCode::RESUPPLY);
            $statement->bindValue('supply_id', $command->id);
            $statement->execute();

            $orderId = $statement->fetchColumn();

            $this->get('uuid.manager')->saveId($command->uuid, $orderId);

            if (empty($orderId)) {
                throw new NotFoundHttpException('Указанного счета поставщика не существует или он уже закрыт для редактирования');
            }

            $sql = '
                INSERT INTO order_item ( order_id, base_product_id, quantity, created_at, created_by ) 
                SELECT
                    :order_id,
                    :base_product_id,
                    SUM( delta ),
                    now( ),
                    :user_id::INTEGER
                FROM
                    supplier_reserve_register 
                WHERE
                    order_item_id IS NULL 
                    AND supply_id = :supply_id 
                    AND base_product_id = :base_product_id 
                    AND purchase_price = :purchase_price
                RETURNING id
            ';
            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('user_id', $currentUser->getId());
            $statement->bindValue('order_id', $orderId);
            $statement->bindValue('purchase_price', $command->purchasePrice);
            $statement->bindValue('base_product_id', $command->baseProductId);
            $statement->bindValue('supply_id', $command->id);
            $statement->execute();

            $orderItemId = $statement->fetchColumn();

            if (empty($orderItemId)) {
                throw new NotFoundHttpException('Позиция заказа не создана');
            }

            $sql = '
                INSERT INTO goods_need_register ( base_product_id, delta, order_item_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                SELECT
                    oi.base_product_id,
                    oi.quantity,
                    oi.id,
                    oi.order_id,
                    :order,
                    o.registered_at,
                    :order_creation,
                    now( ),
                    :user_id::INTEGER 
                FROM
                    order_item AS oi
                    JOIN "order" AS o ON o.id = oi.order_id 
                WHERE
                    oi.id = :order_item_id
            ';
            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('user_id', $currentUser->getId());
            $statement->bindValue('order', DocumentTypeCode::ORDER);
            $statement->bindValue('order_creation', OperationTypeCode::ORDER_CREATION);
            $statement->bindValue('order_item_id', $orderItemId);
            $statement->execute();

            // Меняем регистры
            $sql = '
                INSERT INTO goods_need_register ( base_product_id, delta, order_item_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                SELECT
                    oi.base_product_id,
                    - oi.quantity,
                    oi.id,
                    COALESCE ( ssr.id, sr.id ),
                    :supplier_reserve,
                    COALESCE ( ssr.created_at, sr.created_at ),
                    :supply_item_adding,
                    now( ),
                    :user_id::INTEGER 
                FROM
                    order_item AS oi
                    JOIN supply AS s ON s.id = :supply_id
                    JOIN supplier_reserve AS sr ON sr.supplier_id = s.supplier_id 
                        AND sr.closed_at IS NULL 
                        AND sr.is_shipping = FALSE 
                    LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = s.supplier_id 
                        AND ssr.closed_at IS NULL 
                        AND ssr.is_shipping = TRUE 
                WHERE
                    oi.id = :order_item_id
            ';
            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('supplier_reserve', GoodsNeedRegisterType::SUPPLIER_RESERVE);
            $statement->bindValue('supply_item_adding', OperationTypeCode::SUPPLY_ITEM_ADDING);
            $statement->bindValue('user_id', $currentUser->getId());
            $statement->bindValue('supply_id', $command->id);
            $statement->bindValue('order_item_id', $orderItemId);
            $statement->execute();


            $sql = '
                INSERT INTO supplier_reserve_register ( base_product_id, delta, order_item_id, supplier_reserve_id, supply_id, purchase_price, supplier_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                (
                SELECT
                    srr.base_product_id,
                    SUM( - srr.delta ),
                    NULL,
                    NULL :: INTEGER,
                    s.id,
                    srr.purchase_price,
                    srr.supplier_id,
                    s.id,
                    :supplier_reserve :: document_type_code,
                    s.created_at,
                    :resupply_order_creating :: operation_type_code,
                    now( ),
                    :user_id::INTEGER 
                FROM
                    supplier_reserve_register AS srr
                    JOIN supply AS s ON s.id = srr.supply_id 
                WHERE
                    s.id = :supply_id 
                    AND srr.order_item_id IS NULL 
                    AND srr.purchase_price = :purchase_price 
                    AND srr.base_product_id = :base_product_id 
                GROUP BY
                    srr.base_product_id,
                    srr.purchase_price,
                    s.id,
                    srr.supplier_id 
                HAVING
                    SUM( srr.delta ) > 0 
                ) 
                
                UNION ALL
                
                (
                SELECT
                    srr.base_product_id,
                    SUM( srr.delta ),
                    :order_item_id,
                    NULL :: INTEGER,
                    s.id,
                    srr.purchase_price,
                    srr.supplier_id,
                    s.id,
                    :supplier_reserve :: document_type_code,
                    s.created_at,
                    :resupply_order_creating :: operation_type_code,
                    now( ),
                    :user_id::INTEGER 
                FROM
                    supplier_reserve_register AS srr
                    JOIN supply AS s ON s.id = srr.supply_id 
                WHERE
                    s.id = :supply_id 
                    AND srr.order_item_id IS NULL 
                    AND srr.purchase_price = :purchase_price 
                    AND srr.base_product_id = :base_product_id 
                GROUP BY
                    srr.base_product_id,
                    srr.purchase_price,
                    s.id,
                    srr.supplier_id 
                HAVING
                    SUM( srr.delta ) > 0 
                )
            ';
            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('supplier_reserve', GoodsNeedRegisterType::SUPPLIER_RESERVE);
            $statement->bindValue('resupply_order_creating', OperationTypeCode::RESUPPLY_ORDER_CREATING);
            $statement->bindValue('user_id', $currentUser->getId());
            $statement->bindValue('supply_id', $command->id);
            $statement->bindValue('purchase_price', $command->purchasePrice);
            $statement->bindValue('base_product_id', $command->baseProductId);
            $statement->bindValue('order_item_id', $orderItemId);
            $statement->execute();

            $em->getConnection()->commit();
        } catch (\Exception $ex) {
            $em->getConnection()->rollback();

            throw $ex;
        }
    }
}