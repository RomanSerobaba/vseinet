<?php 

namespace OrderBundle\Bus\Data\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Enum\GoodsNeedRegisterType;
use AppBundle\Enum\OperationTypeCode;
use Doctrine\ORM\Query\ResultSetMapping;
use OrderBundle\Entity\OrderItem;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AnnullCommandHandler extends MessageHandler
{
    public function handle(AnnullCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var \AppBundle\Entity\User $user
         */
        $user = $this->get('user.identity')->getUser();
        $currentUserId = $user->getId();
        $orderAnnulIds = [];

        foreach ($command->ids as $id) {
            /**
             * @var OrderItem $model
             */
            $model = $em->getRepository(OrderItem::class)->find($id);

            if (!$model) {
                throw new NotFoundHttpException();
            }

            $query = $em->createNativeQuery('
                SELECT 
                    COALESCE( delta, 0 ) AS quantity 
                FROM
                    get_goods_need_register_data(CURRENT_TIMESTAMP::TIMESTAMP, NULL, :order_item_id) 
            ', new ResultSetMapping());
            $query->setParameter('order_item_id', $id);

            $rows = $query->getResult('ListAssocHydrator');
            $row = array_shift($rows);
            $needQuantity = $row['quantity'];

            $query = $em->createNativeQuery('
                SELECT 
                    COALESCE( SUM ( delta ), 0 ) AS quantity 
                FROM
                    supplier_reserve_register 
                WHERE
                    order_item_id = :order_item_id
            ', new ResultSetMapping());
            $query->setParameter('order_item_id', $id);

            $rows = $query->getResult('ListAssocHydrator');
            $row = array_shift($rows);
            $supplierQuantity = $row['quantity'];

            $query = $em->createNativeQuery('
                SELECT 
                    COALESCE( SUM ( delta ), 0 ) AS quantity 
                FROM
                    get_goods_reserve_register_data(CURRENT_TIMESTAMP::TIMESTAMP, 0, 0, :reserved, 0, :order_item_id) 
            ', new ResultSetMapping());
            $query->setParameter('order_item_id', $id);
            $query->setParameter('reserved', GoodsConditionCode::RESERVED);

            $rows = $query->getResult('ListAssocHydrator');
            $row = array_shift($rows);
            $reservedQuantity = $row['quantity'];

            $quantity =  $needQuantity +  $supplierQuantity +  $reservedQuantity;

            $em->getConnection()->beginTransaction();
            try {
                if (!empty($quantity)) {
                    $statement = $em->getConnection()->prepare('
                        INSERT INTO order_annul ( order_annul_cause_code, comment, created_at, created_by, is_reserve_canceled, is_client_offender )
                        VALUES ( :cause_code, :comment, now(), :user_id::INTEGER, :is_reserve_canceled, :is_client_offender ) 
                        RETURNING id
                    ');
                    $statement->bindValue('cause_code', $command->causeCode);
                    $statement->bindValue('comment', $command->comment);
                    $statement->bindValue('user_id', $currentUserId);
                    $statement->bindValue('is_reserve_canceled', false, \PDO::PARAM_BOOL);
                    $statement->bindValue('is_client_offender', (boolean) $command->isClientFault, \PDO::PARAM_BOOL);
                    $statement->execute();

                    $orderAnnulId = $statement->fetchColumn();

                    if (!empty($orderAnnulId)) {
                        $query = $em->createNativeQuery('
                            INSERT INTO order_annul_item ( order_item_id, order_annul_id, quantity )
                            VALUES ( :order_item_id, :order_annul_id, :quantity )
                        ', new ResultSetMapping());
                        $query->setParameter('order_item_id', $id);
                        $query->setParameter('order_annul_id', $orderAnnulId);
                        $query->setParameter('quantity', $quantity);

                        $query->execute();

                        $orderAnnulIds[] = $orderAnnulId;
                    } else {
                        throw new BadRequestHttpException('Ошибка создания записи в таблице order_annul');
                    }
                } else {
                    throw new BadRequestHttpException('Статус позиции не позволяет произвести отмену');
                }

                // Добавляем записи в регистр
                if ( $supplierQuantity  > 0) {
                    $statement = $em->getConnection()->prepare('
                        INSERT INTO goods_need_register ( delta, order_item_id, registrator_id, registered_at, register_operation_type_code, registrator_type_code, base_product_id, created_at, created_by ) 
                        SELECT
                            SUM( srr.delta )::INTEGER AS delta,
                            oi.id AS order_item_id,
                            srr.supplier_reserve_id AS registrator_id,
                            NOW( ) AS registered_at,
                            :supplier_goods_reservation AS register_operation_type_code,
                            :supplier_reserve AS registrator_type_code,
                            oi.base_product_id,
                            NOW( ) AS created_at,
                            :user_id::INTEGER AS created_by 
                        FROM
                            order_item AS oi
                            JOIN supplier_reserve_register AS srr ON srr.order_item_id = oi.id 
                        WHERE
                            oi.id = :order_item_id 
                        GROUP BY
                            srr.supplier_reserve_id,
                            oi.id 
                        HAVING
                            SUM( srr.delta ) > 0      
                    ');
                    $statement->bindValue('supplier_goods_reservation', OperationTypeCode::SUPPLIER_GOODS_RESERVATION);
                    $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
                    $statement->bindValue('order_item_id', $id);
                    $statement->bindValue('user_id', $currentUserId);
                    $statement->execute();

                    $statement = $em->getConnection()->prepare('
                        INSERT INTO supplier_reserve_register ( base_product_id, delta, order_item_id, supplier_id, supplier_reserve_id, supply_id, purchase_price, registrator_id, registered_at, register_operation_type_code, registrator_type_code, created_at, created_by ) 
                        SELECT
                            base_product_id,
                            -SUM( delta )::INTEGER AS delta,
                            order_item_id,
                            supplier_id,
                            supplier_reserve_id,
                            supply_id,
                            purchase_price,
                            supplier_reserve_id AS registrator_id,
                            NOW( ) AS registered_at,
                            :supplier_goods_reservation AS register_operation_type_code,
                            :supplier_reserve AS registrator_type_code,
                            NOW( ) AS created_at,
                            :user_id::INTEGER AS created_by 
                        FROM
                            supplier_reserve_register 
                        WHERE
                            order_item_id = :order_item_id 
                        GROUP BY
                            base_product_id,
                            order_item_id,
                            supplier_id,
                            supplier_reserve_id,
                            supply_id,
                            purchase_price 
                        HAVING
                            SUM( delta ) > 0      
                    ');
                    $statement->bindValue('supplier_goods_reservation', OperationTypeCode::SUPPLIER_GOODS_RESERVATION);
                    $statement->bindValue('supplier_reserve', GoodsNeedRegisterType::SUPPLIER_RESERVE);
                    $statement->bindValue('order_item_id', $id);
                    $statement->bindValue('user_id', $currentUserId);
                    $statement->execute();
                }

                if ($reservedQuantity > 0) {
                    $query = $em->createNativeQuery('
                        INSERT INTO available_goods_reservation ( created_at, created_by, parent_doc_type, parent_doc_id, order_item_id )
                        VALUES ( NOW( ), :user_id::INTEGER, :order_annul, :order_annul_id::INTEGER, :order_item_id::INTEGER ) 
                        RETURNING id
                    ', new ResultSetMapping());
                    $query->setParameter('user_id', $currentUserId);
                    $query->setParameter('order_annul', OperationTypeCode::ORDER_ANNUL);
                    $query->setParameter('order_annul_id', $orderAnnulId);
                    $query->setParameter('order_item_id', $id);

                    $availableGoodsReservationId = $query->execute();

                    $statement = $em->getConnection()->prepare('
                        INSERT INTO available_goods_reservation_item ( available_goods_reservation_id, delta, geo_point_id, is_in_transit ) 
                        SELECT
                            :available_goods_reservation_id,
                            -SUM( delta )::INTEGER,
                            gr.geo_point_id,
                            CASE WHEN gr.geo_point_id > 0 
                                THEN FALSE 
                                ELSE TRUE 
                            END 
                        FROM
                            get_goods_reserve_register_data(CURRENT_TIMESTAMP::TIMESTAMP, 0, 0, NULL, 0, :order_item_id) AS grr
                            LEFT JOIN geo_room AS gr ON gr.id = grr.geo_room_id 
                        GROUP BY
                            gr.id    
                    ');
                    $statement->bindValue('available_goods_reservation_id', $availableGoodsReservationId);
                    $statement->bindValue('order_item_id', $id);
                    $statement->execute();

                    $statement = $em->getConnection()->prepare('
                        INSERT INTO goods_reserve_register (
                            base_product_id,
                            supply_item_id,
                            goods_condition_code,
                            geo_room_id,
                            order_item_id,
                            goods_release_id,
                            goods_pallet_id,
                            delta,
                            registrator_id,
                            registered_at,
                            register_operation_type_code,
                            registrator_type_code,
                            created_at,
                            created_by 
                        ) (
                        SELECT
                            base_product_id,
                            supply_item_id,
                            :reserved :: goods_condition_code,
                            geo_room_id,
                            order_item_id,
                            goods_release_id,
                            goods_pallet_id,
                            -delta,
                            :available_goods_reservation_id AS registrator_id,
                            NOW( ) AS registered_at,
                            :available_goods_reservation_operation :: operation_type_code AS register_operation_type_code,
                            :available_goods_reservation :: document_type_code AS registrator_type_code,
                            NOW( ) AS created_at,
                            :user_id::INTEGER AS created_by 
                        FROM
                            get_goods_reserve_register_data(CURRENT_TIMESTAMP::TIMESTAMP, 0, 0, :reserved, 0, :order_item_id) 
                        ) 
                        UNION ALL
                        (
                        SELECT
                            base_product_id,
                            supply_item_id,
                            :free :: goods_condition_code,
                            geo_room_id,
                            NULL,
                            goods_release_id,
                            goods_pallet_id,
                            delta,
                            :available_goods_reservation_id AS registrator_id,
                            NOW( ) AS registered_at,
                            :available_goods_reservation_operation :: operation_type_code AS register_operation_type_code,
                            :available_goods_reservation :: document_type_code AS registrator_type_code,
                            NOW( ) AS created_at,
                            :user_id::INTEGER AS created_by 
                        FROM
                            get_goods_reserve_register_data(CURRENT_TIMESTAMP::TIMESTAMP, 0, 0, :reserved, 0, :order_item_id) 
                        )  
                    ');
                    $statement->bindValue('available_goods_reservation_id', $availableGoodsReservationId);
                    $statement->bindValue('order_item_id', $id);
                    $statement->bindValue('user_id', $currentUserId);
                    $statement->bindValue('reserved', GoodsConditionCode::RESERVED);
                    $statement->bindValue('available_goods_reservation_operation', OperationTypeCode::AVAILABLE_GOODS_RESERVATION);
                    $statement->bindValue('available_goods_reservation', DocumentTypeCode::AVAILABLE_GOODS_RESERVATION);
                    $statement->bindValue('free', GoodsConditionCode::FREE);
                    $statement->execute();
                }

                $statement = $em->getConnection()->prepare('
                    INSERT INTO goods_need_register ( delta, order_item_id, registrator_id, registered_at, register_operation_type_code, registrator_type_code, base_product_id, created_at, created_by ) 
                    SELECT
                        -:quantity::INTEGER AS delta,
                        oi.id AS order_item_id,
                        :order_annul_id AS registrator_id,
                        NOW() AS registered_at,
                        :order_annul_operation AS register_operation_type_code,
                        :order_annul AS registrator_type_code,
                        oi.base_product_id,
                        NOW( ) AS created_at,
                        :user_id::INTEGER AS created_by 
                    FROM
                        order_item AS oi 
                    WHERE
                        oi.id = :order_item_id 
                ');
                $statement->bindValue('quantity', $quantity);
                $statement->bindValue('order_item_id', $id);
                $statement->bindValue('user_id', $currentUserId);
                $statement->bindValue('order_annul_id', $orderAnnulId);
                $statement->bindValue('order_annul_operation', OperationTypeCode::ORDER_ANNUL);
                $statement->bindValue('order_annul', DocumentTypeCode::ORDER_ANNUL);
                $statement->execute();

                $em->getConnection()->commit();
            } catch (\Exception $ex) {
                $em->getConnection()->rollback();

                throw $ex;
            }
        }

        return $orderAnnulIds;
    }
}