<?php 

namespace SupplyBundle\Bus\Data\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Enum\GoodsNeedRegisterType;
use AppBundle\Enum\OperationTypeCode;
use AppBundle\Enum\OrderTypeCode;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CloseSupplierInvoiceCommandHandler extends MessageHandler
{
    public function handle(CloseSupplierInvoiceCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $em->getConnection()->beginTransaction();
        try {

            $sql = '
                UPDATE supply 
                SET registered_at = NOW( ),
                    registered_by = :user_id::INTEGER,
                    supplier_counteragent_id = :supplier_counteragent_id,
                    comment = :comment 
                WHERE
                    id = :supply_id 
                    AND registered_at IS NULL 
                RETURNING id
            ';
            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('supply_id', $command->id);
            $statement->bindValue('user_id', $currentUser->getId());
            $statement->bindValue('supplier_counteragent_id', $command->supplierCounteragentId);
            $statement->bindValue('comment', $command->comment);
            $statement->execute();

            $id = $statement->fetchColumn();

            if ($id > 0) {
                // Создаем позиции счета
                $sql = '
                    INSERT INTO supply_item ( parent_doc_type, parent_doc_id, quantity, base_product_id, purchase_price ) 
                    SELECT
                        :supply,
                        supply_id,
                        SUM( delta ),
                        base_product_id,
                        purchase_price 
                    FROM
                        supplier_reserve_register 
                    WHERE
                        supply_id = :supply_id 
                    GROUP BY
                        base_product_id,
                        purchase_price,
                        supply_id 
                    HAVING
                        SUM( delta ) > 0
                ';
                $statement = $em->getConnection()->prepare($sql);
                $statement->bindValue('supply', DocumentTypeCode::SUPPLY);
                $statement->bindValue('supply_id', $command->id);
                $statement->execute();

                // Меняем регистры потребностей
                $sql = '
                    INSERT INTO goods_need_register ( base_product_id, delta, order_item_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                    SELECT
                        srr.base_product_id,
                        SUM( - srr.delta ),
                        srr.order_item_id,
                        s.id,
                        :supply,
                        s.registered_at,
                        :supply_shipping,
                        now( ),
                        :user_id::INTEGER 
                    FROM
                        supplier_reserve_register AS srr
                        JOIN supply AS s ON s.id = srr.supply_id 
                    WHERE
                        srr.supply_id = :supply_id 
                        AND srr.supplier_reserve_id IS NULL 
                        AND srr.order_item_id > 0 
                    GROUP BY
                        srr.base_product_id,
                        srr.order_item_id,
                        s.id 
                    HAVING
                        SUM( srr.delta ) > 0
                ';
                $statement = $em->getConnection()->prepare($sql);
                $statement->bindValue('supply', DocumentTypeCode::SUPPLY);
                $statement->bindValue('supply_shipping', OperationTypeCode::SUPPLY_SHIPPING);
                $statement->bindValue('user_id', $currentUser->getId());
                $statement->bindValue('supply_id', $command->id);
                $statement->execute();

                // Меняем регистры резервов поставщика
                $sql = '
                    INSERT INTO supplier_reserve_register ( base_product_id, order_item_id, delta, supplier_id, supply_id, supplier_reserve_id, purchase_price, registrator_type_code, registrator_id, register_operation_type_code, registered_at, created_at, created_by ) 
                    SELECT
                        srr.base_product_id,
                        srr.order_item_id,
                        SUM( - srr.delta ),
                        srr.supplier_id,
                        s.id,
                        srr.supplier_reserve_id,
                        srr.purchase_price,
                        :supply,
                        s.id,
                        :supply_shipping,
                        s.registered_at,
                        now( ),
                        :user_id::INTEGER 
                    FROM
                        supplier_reserve_register AS srr
                        JOIN supply AS s ON s.id = srr.supply_id 
                    WHERE
                        srr.supply_id = :supply_id 
                    GROUP BY
                        srr.base_product_id,
                        srr.order_item_id,
                        s.id,
                        srr.supplier_id,
                        srr.supplier_reserve_id,
                        srr.purchase_price 
                    HAVING
                        SUM( srr.delta ) > 0
                ';
                $statement = $em->getConnection()->prepare($sql);
                $statement->bindValue('supply', DocumentTypeCode::SUPPLY);
                $statement->bindValue('supply_shipping', OperationTypeCode::SUPPLY_SHIPPING);
                $statement->bindValue('user_id', $currentUser->getId());
                $statement->bindValue('supply_id', $command->id);
                $statement->execute();

                // и резервов
                $sql = '
                    INSERT INTO goods_reserve_register ( base_product_id, supply_item_id, goods_condition_code, order_item_id, delta, registrator_type_code, registrator_id, register_operation_type_code, registered_at, created_at, created_by ) 
                    SELECT
                        srr.base_product_id,
                        si.id,
                        CASE 
                            WHEN o.type_code = :type_resupply THEN :free :: goods_condition_code 
                            WHEN o.type_code = :type_equipment THEN :equipment :: goods_condition_code 
                            ELSE :reserved :: goods_condition_code 
                        END,
                        srr.order_item_id,
                        SUM( srr.delta ),
                        :supply,
                        s.id,
                        :supply_shipping,
                        s.registered_at,
                        now( ),
                        :user_id::INTEGER 
                    FROM
                        supplier_reserve_register AS srr
                        JOIN supply AS s ON s.id = srr.supply_id
                        JOIN order_item AS oi ON oi.id = srr.order_item_id
                        JOIN "order" AS o ON o.id = oi.order_id
                        JOIN supply_item AS si ON si.parent_doc_type = :supply 
                            AND si.parent_doc_id = s.id 
                            AND si.base_product_id = srr.base_product_id 
                            AND si.purchase_price = srr.purchase_price 
                    WHERE
                        srr.supply_id = :supply_id 
                        AND srr.order_item_id > 0 
                    GROUP BY
                        srr.base_product_id,
                        srr.order_item_id,
                        s.id,
                        srr.supplier_id,
                        srr.supplier_reserve_id,
                        srr.purchase_price,
                        si.id,
                        o.id 
                    HAVING
                        SUM( srr.delta ) > 0
                ';
                $statement = $em->getConnection()->prepare($sql);
                $statement->bindValue('supply', DocumentTypeCode::SUPPLY);
                $statement->bindValue('supply_shipping', OperationTypeCode::SUPPLY_SHIPPING);
                $statement->bindValue('user_id', $currentUser->getId());
                $statement->bindValue('supply_id', $command->id);
                $statement->bindValue('free', GoodsConditionCode::FREE);
                $statement->bindValue('type_resupply', OrderTypeCode::RESUPPLY);
                $statement->bindValue('type_equipment', OrderTypeCode::EQUIPMENT);
                $statement->bindValue('equipment', GoodsConditionCode::EQUIPMENT);
                $statement->bindValue('reserved', GoodsConditionCode::RESERVED);
                $statement->execute();

                // Создаем документ приходования
                $sql = '
                    INSERT INTO goods_acceptance_doc ( parent_doc_id, parent_doc_type, created_at, created_by, geo_room_id, arriving_time, title ) 
                    SELECT
                        id,
                        :supply,
                        now( ),
                        :user_id::INTEGER,
                        ( SELECT id FROM geo_room WHERE geo_point_id = destination_point_id AND is_default = TRUE LIMIT 1 ),
                        :arriving_time,
                        :title
                    FROM
                        supply 
                    WHERE
                        id = :supply_id 
                    RETURNING id
                ';
                $statement = $em->getConnection()->prepare($sql);
                $statement->bindValue('supply', DocumentTypeCode::SUPPLY);
                $statement->bindValue('user_id', $currentUser->getId());
                $statement->bindValue('supply_id', $command->id);
                $statement->bindValue('arriving_time', $command->arrivingTime, Type::DATETIME);
                $statement->bindValue('title', 'Закрытие счета №'.$command->id);
                $statement->execute();

                $goodsAcceptanceId = $statement->fetchColumn();

                if ($goodsAcceptanceId) {
                    $sql = '
                        INSERT INTO goods_acceptance_item ( goods_acceptance_id, base_product_id, initial_quantity, order_item_id ) 
                        SELECT
                            :accepatance_id,
                            grr.base_product_id,
                            grr.delta,
                            grr.order_item_id 
                        FROM
                            get_goods_reserve_register_data ( CURRENT_TIMESTAMP :: TIMESTAMP ) AS grr
                            JOIN supply_item AS si ON si.id = grr.supply_item_id 
                        WHERE
                            si.parent_doc_type = :supply
                            AND si.parent_doc_id = :supply_id
                    ';
                    $statement = $em->getConnection()->prepare($sql);
                    $statement->bindValue('supply', DocumentTypeCode::SUPPLY);
                    $statement->bindValue('supply_id', $command->id);
                    $statement->bindValue('accepatance_id', $goodsAcceptanceId);
                    $statement->execute();
                }
            } else {
                throw new BadRequestHttpException('Невозможно закрыть уже закрытый счет');
            }

            $em->getConnection()->commit();
        } catch (\Exception $ex) {
            $em->getConnection()->rollback();

            throw $ex;
        }
    }
}