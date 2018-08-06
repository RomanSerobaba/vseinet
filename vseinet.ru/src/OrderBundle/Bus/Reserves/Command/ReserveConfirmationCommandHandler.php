<?php

namespace OrderBundle\Bus\Reserves\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Enum\GoodsNeedRegisterType;
use AppBundle\Enum\OperationTypeCode;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;
use OrderBundle\Entity\OrderItem;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReserveConfirmationCommandHandler extends MessageHandler
{
    public function handle(ReserveConfirmationCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $orderItem = $em->getRepository(OrderItem::class)->find($command->id);

        if (empty($orderItem)) {
            throw new NotFoundHttpException('Order item not found');
        }

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $sumReservingQuantities = 0;
        foreach ($command->reservingQuantities as $reservingQuantity) { // [['pointId' => 1, 'quantity' => 7, 'isInTransit' => false], ...]
            $sumReservingQuantities += $reservingQuantity['quantity'];
        }

        if ($sumReservingQuantities == 0) {
            return;
        }

        $em->getConnection()->beginTransaction();
        try {

            $query = $em->createNativeQuery("
                SELECT 
                    COALESCE(SUM(delta), 0)::int AS quantity 
                FROM
                    goods_need_register 
                WHERE
                    order_item_id = :order_item_id        
            ", new ResultSetMapping());
            $query->setParameter('order_item_id', $command->id);
            $rows = $query->getResult('ListAssocHydrator');
            $row = array_shift($rows);

            $processingQuantity = $row['quantity'];

            $query = $em->createNativeQuery("
                SELECT
                    SUM( srr.delta ) :: INTEGER AS quantity 
                FROM
                    supplier_reserve_register AS srr
                    JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id 
                WHERE
                    srr.order_item_id = :order_item_id 
                    AND sr.closed_at IS NULL
            ", new ResultSetMapping());
            $query->setParameter('order_item_id', $command->id);

            $rows = $query->getResult('ListAssocHydrator');
            $row = array_shift($rows);

            $supplierQuantity = $row['quantity'];

            $needQuantity = $processingQuantity + $supplierQuantity;

            if ($needQuantity  < $sumReservingQuantities) {
                throw new BadRequestHttpException('Вы пытаетесь зарезервировать больше товара, чем требуется по заказу');
            }

            $reservedQuantities = [];
            foreach ($command->reservingQuantities as $reservingQuantity) {
                if ($reservingQuantity['quantity'] <= 0) {
                    continue;
                }

                $query = $em->createNativeQuery("
                    SELECT
                        r.quantity AS reserve_quantity,
                        r.goods_release_id,
                        r.geo_room_id,
                        r.supply_item_id,
                        r.order_item_id 
                    FROM (
                        SELECT
                            grr.geo_room_id,
                            grr.goods_release_id,
                            grr.supply_item_id,
                            grr.order_item_id,
                            SUM ( grr.delta ) AS quantity 
                        FROM
                            get_goods_reserve_register_data ( CURRENT_TIMESTAMP :: TIMESTAMP, 0, 0, :free ) AS grr
                            JOIN order_item AS oi ON oi.base_product_id = grr.base_product_id 
                        WHERE
                            oi.id = :order_item_id 
                        GROUP BY
                            grr.supply_item_id,
                            grr.goods_release_id,
                            grr.geo_room_id,
                            grr.order_item_id 
                        HAVING
                            SUM ( grr.delta ) > 0 
                        ) AS r
                        LEFT JOIN supply_item AS si ON si.id = r.supply_item_id
                        LEFT JOIN supply AS s ON si.parent_doc_type = :supply
                            AND s.id = si.parent_doc_id
                        LEFT JOIN goods_release_doc AS gre ON gre.number = r.goods_release_id
                        LEFT JOIN geo_room AS gr ON gr.id = COALESCE ( r.geo_room_id, gre.destination_room_id )
                        JOIN view_geo_point AS vgp ON vgp.id = COALESCE ( gr.geo_point_id, s.destination_point_id ) 
                    WHERE
                        vgp.id = :geo_point_id 
                        AND CASE WHEN r.geo_room_id IS NULL THEN TRUE ELSE FALSE END = :is_in_transit
                ", new ResultSetMapping());
                $query->setParameter('order_item_id', $command->id);
                $query->setParameter('free', GoodsConditionCode::FREE);
                $query->setParameter('supply', DocumentTypeCode::SUPPLY);
                $query->setParameter('geo_point_id', $reservingQuantity['pointId']);
                $query->setParameter('is_in_transit', $reservingQuantity['isInTransit'], "boolean");

                $reserves = $query->getResult('ListAssocHydrator');
                $sum = 0;

                foreach ($reserves as $reserve) {
                    $sum += $reserve['reserve_quantity'];
                }
                
                if ($reservingQuantity['quantity'] > $sum) {
                    throw new BadRequestHttpException('Вы пытаетесь зарезервировать больше товара, чем есть в наличии на точке');
                }

                foreach ($reserves as $reserve) {
                    $reservedQuantities[] = [
                        'supply_item_id' => $reserve['supply_item_id'],
                        'goods_release_id' => $reserve['goods_release_id'],
                        'geo_room_id' => $reserve['geo_room_id'],
                        'order_item_id' => $reserve['order_item_id'],
                        'geo_point_id' => $reservingQuantity['pointId'],
                        'is_in_transit' => $reservingQuantity['isInTransit'],
                        'quantity' => $reserve['reserve_quantity'] >= $reservingQuantity['quantity'] ? $reservingQuantity['quantity']  : $reserve['reserve_quantity'],
                    ];

                    if ($reserve['reserve_quantity'] >= $reservingQuantity['quantity'] ) {
                        break;
                    }

                    $reservingQuantity['quantity']  -= $reserve['reserve_quantity'];
                }
            }

            // Создаем документ резервирования
            $sql = '
                INSERT INTO available_goods_reservation (order_item_id, created_at, created_by)
                VALUES (:order_item_id, NOW(), :user_id::INTEGER) 
                RETURNING id
            ';
            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('order_item_id', $command->id);
            $statement->bindValue('user_id', $currentUser->getId());
            $statement->execute();

            $availableGoodsReservationId = $statement->fetchColumn();

            foreach ($command->reservingQuantities as $reservingQuantity) {
                // Добавляем строку в документ резервирования с наличия
                $sql = '
                    INSERT INTO available_goods_reservation_item (available_goods_reservation_id, delta, geo_point_id, is_in_transit)
                    VALUES (:available_goods_reservation_id, :quantity, :geo_point_id, :is_in_transit )
                ';
                $statement = $em->getConnection()->prepare($sql);
                $statement->bindValue('available_goods_reservation_id', $availableGoodsReservationId);
                $statement->bindValue('quantity', $reservingQuantity['quantity']);
                $statement->bindValue('geo_point_id', $reservingQuantity['pointId']);
                $statement->bindValue('is_in_transit', (bool) $reservingQuantity['isInTransit'], Type::BOOLEAN);
                $statement->execute();
            }

            foreach ($reservedQuantities as $reservedQuantity) {
                if ($reservedQuantity['order_item_id'] > 0) { // Если резервируем с заявки
                    // Создаем документ аннулирования
                    $sql = '
                        INSERT INTO order_annul ( created_at, created_by, is_reserve_canceled, parent_doc_id, parent_doc_type )
                        VALUES ( NOW( ), :user_id::INTEGER, FALSE, :available_goods_reservation_id, :available_goods_reservation) 
                        RETURNING id
                    ';
                    $statement = $em->getConnection()->prepare($sql);
                    $statement->bindValue('user_id', $currentUser->getId());
                    $statement->bindValue('available_goods_reservation_id', $availableGoodsReservationId);
                    $statement->bindValue('available_goods_reservation', DocumentTypeCode::AVAILABLE_GOODS_RESERVATION);
                    $statement->execute();

                    $annulId = $statement->fetchColumn();

                    // Добавляем строку в документ аннулирования
                    $sql = '
                        INSERT INTO order_annul_item ( order_item_id, order_annul_id, quantity )
                        VALUES ( :order_item_id, :order_annul_id, :quantity )
                    ';
                    $statement = $em->getConnection()->prepare($sql);
                    $statement->bindValue('order_item_id', $command->id);
                    $statement->bindValue('order_annul_id', $annulId);
                    $statement->bindValue('quantity',  (integer) $reservedQuantity['quantity'], 'integer');
                    $statement->execute();

                    // Добавляем запись в регистр по аннулированию
                    $sql = '
                        INSERT INTO goods_need_register ( base_product_id, delta, order_item_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                        SELECT
                            oi.base_product_id,
                            -:quantity :: integer,
                            oi.id AS order_item_id,
                            :order_annul AS registrator_type_code,
                            oa.id AS registrator_id,
                            :order_annul_operation AS register_operation_type_code,
                            oa.created_at AS registered_at,
                            now( ),
                            :user_id::INTEGER
                        FROM
                            order_item AS oi
                            JOIN "order" AS o ON o.id = oi.order_id
                            JOIN order_annul AS oa ON oa.id = :order_annul_id 
                        WHERE
                            oi.id = :order_item_id
                    ';

                    $statement = $em->getConnection()->prepare($sql);
                    $statement->bindValue('quantity', (integer) $reservedQuantity['quantity'], 'integer');
                    $statement->bindValue('order_annul', DocumentTypeCode::ORDER_ANNUL);
                    $statement->bindValue('order_annul_operation', OperationTypeCode::ORDER_ANNUL);
                    $statement->bindValue('user_id', $currentUser->getId());
                    $statement->bindValue('order_annul_id', $annulId);
                    $statement->bindValue('order_item_id', $reservedQuantity['order_item_id']);
                    $statement->execute();

                    // Создаем дочерний документ изменения резерва с наличия
                    $sql = '
                        INSERT INTO available_goods_reservation ( order_item_id, created_at, created_by, parent_doc_type, parent_doc_id )
                        VALUES ( :order_item_id, now( ), :user_id::INTEGER, :order_annul, :order_annul_id ) 
                        RETURNING id
                    ';
                    $statement = $em->getConnection()->prepare($sql);
                    $statement->bindValue('order_item_id', $reservedQuantity['order_item_id']);
                    $statement->bindValue('user_id', $currentUser->getId());
                    $statement->bindValue('order_annul', DocumentTypeCode::ORDER_ANNUL);
                    $statement->bindValue('order_annul_id', $annulId);
                    $statement->execute();

                    $childAvailableGoodsReservationId = $statement->fetchColumn();

                    // добавляем в него запись
                    $sql = '
                        INSERT INTO available_goods_reservation_item ( available_goods_reservation_id, delta, geo_point_id, is_in_transit )
                        VALUES ( :child_available_goods_reservation_id, -:quantity, :geo_point_id, :is_in_transit )
                    ';
                    $statement = $em->getConnection()->prepare($sql);
                    $statement->bindValue('child_available_goods_reservation_id', $childAvailableGoodsReservationId);
                    $statement->bindValue('quantity', (integer) $reservedQuantity['quantity'], 'integer');
                    $statement->bindValue('geo_point_id', $reservedQuantity['geo_point_id']);
                    $statement->bindValue('is_in_transit', (bool) $reservedQuantity['is_in_transit'], Type::BOOLEAN);
                    $statement->execute();

                    // добавляем запись в регистры по дочернему документу изменения резерва с наличия
                    $sql = '
                        INSERT INTO goods_need_register ( base_product_id, delta, order_item_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                        SELECT
                            oi.base_product_id,
                            :quantity,
                            oi.id AS order_item_id,
                            :available_goods_reservation AS registrator_type_code,
                            agr.id AS registrator_id,
                            :available_goods_reservation_operation AS register_operation_type_code,
                            agr.created_at AS registered_at,
                            now( ),
                            :user_id::INTEGER
                        FROM
                            order_item AS oi
                            JOIN "order" AS o ON o.id = oi.order_id
                            JOIN available_goods_reservation AS agr ON agr.id = :child_available_goods_reservation_id 
                        WHERE
                            oi.id = :order_item_id
                    ';

                    $statement = $em->getConnection()->prepare($sql);
                    $statement->bindValue('quantity', (integer) $reservedQuantity['quantity'], 'integer');
                    $statement->bindValue('available_goods_reservation', DocumentTypeCode::AVAILABLE_GOODS_RESERVATION);
                    $statement->bindValue('available_goods_reservation_operation', OperationTypeCode::AVAILABLE_GOODS_RESERVATION);
                    $statement->bindValue('user_id', $currentUser->getId());
                    $statement->bindValue('child_available_goods_reservation_id', $childAvailableGoodsReservationId);
                    $statement->bindValue('order_item_id', $reservedQuantity['order_item_id']);
                    $statement->execute();

                    $sql = '
                        INSERT INTO goods_reserve_register ( base_product_id, supply_item_id, goods_condition_code, geo_room_id, order_item_id, goods_release_id, delta, registrator_type_code, registrator_id, register_operation_type_code, registered_at, created_at, created_by ) 
                        (
                        SELECT
                            oi.base_product_id,
                            :supply_item_id :: integer,
                            :free :: goods_condition_code AS goods_condition_code,
                            :geo_room_id :: integer,
                            :order_item_id :: integer AS order_item_id,
                            :goods_release_id :: integer AS goods_release_id,
                            -:quantity :: integer AS delta,
                            :order_annul :: document_type_code AS registrator_type_code,
                            agr.id AS registrator_id,
                            :order_annul_operation :: operation_type_code AS register_operation_type_code,
                            agr.created_at AS registered_at,
                            now( ) AS created_at,
                            :user_id::INTEGER AS created_by 
                        FROM
                            order_annul AS agr
                            INNER JOIN order_item AS oi ON oi.id = :id 
                        WHERE
                            agr.id = :child_available_goods_reservation_id 
                            
                        UNION ALL
                        
                        SELECT
                            oi.base_product_id,
                            :supply_item_id :: integer,
                            :free :: goods_condition_code AS goods_condition_code,
                            :geo_room_id :: integer,
                            NULL :: integer AS order_item_id,
                            :goods_release_id :: integer AS goods_release_id,
                            :quantity AS delta,
                            :order_annul :: document_type_code AS registrator_type_code,
                            agr.id AS registrator_id,
                            :order_annul_operation :: operation_type_code AS register_operation_type_code,
                            agr.created_at AS registered_at,
                            now( ) AS created_at,
                            :user_id::INTEGER AS created_by 
                        FROM
                            order_annul AS agr
                            INNER JOIN order_item AS oi ON oi.id = :id 
                        WHERE
                            agr.id = :child_available_goods_reservation_id 
                        )
                    ';

                    $statement = $em->getConnection()->prepare($sql);
                    $statement->bindValue('order_annul', DocumentTypeCode::ORDER_ANNUL);
                    $statement->bindValue('order_annul_operation', OperationTypeCode::ORDER_ANNUL);
                    $statement->bindValue('supply_item_id', $reservedQuantity['supply_item_id']);
                    $statement->bindValue('free', GoodsConditionCode::FREE);
                    $statement->bindValue('order_item_id', $reservedQuantity['order_item_id']);
                    $statement->bindValue('geo_room_id', $reservedQuantity['geo_room_id']);
                    $statement->bindValue('goods_release_id', $reservedQuantity['goods_release_id']);
                    $statement->bindValue('quantity', (integer) $reservedQuantity['quantity'], 'integer');
                    $statement->bindValue('id', $command->id);
                    $statement->bindValue('child_available_goods_reservation_id', $childAvailableGoodsReservationId);
                    $statement->execute();
                }

                $sql = '
                    INSERT INTO goods_reserve_register ( base_product_id, supply_item_id, goods_condition_code, geo_room_id, order_item_id, goods_release_id, delta, registrator_type_code, registrator_id, register_operation_type_code, registered_at, created_at, created_by ) 
                    (
                    SELECT
                        oi.base_product_id,
                        :supply_item_id :: integer,
                        :reserved :: goods_condition_code AS goods_condition_code,
                        :geo_room_id :: integer,
                        NULL :: integer AS order_item_id,
                        :goods_release_id :: integer AS goods_release_id,
                        -:quantity :: integer AS delta,
                        :available_goods_reservation :: document_type_code AS registrator_type_code,
                        agr.id AS registrator_id,
                        :available_goods_reservation_operation :: operation_type_code AS register_operation_type_code,
                        agr.created_at AS registered_at,
                        now( ) AS created_at,
                        :user_id::INTEGER AS created_by 
                    FROM
                        available_goods_reservation AS agr
                        INNER JOIN order_item AS oi ON oi.id = agr.order_item_id 
                    WHERE
                        agr.id = :available_goods_reservation_id 
                    
                    UNION ALL
                    
                    SELECT
                        oi.base_product_id,
                        :supply_item_id :: integer,
                        :reserved :: goods_condition_code AS goods_condition_code,
                        :geo_room_id :: integer,
                        :order_item_id AS order_item_id,
                        :goods_release_id :: integer AS goods_release_id,
                        :quantity AS delta,
                        :available_goods_reservation :: document_type_code AS registrator_type_code,
                        agr.id AS registrator_id,
                        :available_goods_reservation_operation :: operation_type_code AS register_operation_type_code,
                        agr.created_at AS registered_at,
                        now( ) AS created_at,
                        :user_id::INTEGER AS created_by 
                    FROM
                        available_goods_reservation AS agr
                        INNER JOIN order_item AS oi ON oi.id = agr.order_item_id 
                    WHERE
                        agr.id = :available_goods_reservation_id 
                    )
                ';

                $statement = $em->getConnection()->prepare($sql);
                $statement->bindValue('supply_item_id', $reservedQuantity['supply_item_id']);
                $statement->bindValue('reserved', GoodsConditionCode::RESERVED);
                $statement->bindValue('geo_room_id', $reservedQuantity['geo_room_id']);
                $statement->bindValue('order_item_id', $command->id);
                $statement->bindValue('goods_release_id', $reservedQuantity['goods_release_id']);
                $statement->bindValue('quantity', (integer) $reservedQuantity['quantity'], 'integer');
                $statement->bindValue('available_goods_reservation', DocumentTypeCode::AVAILABLE_GOODS_RESERVATION);
                $statement->bindValue('available_goods_reservation_operation', OperationTypeCode::AVAILABLE_GOODS_RESERVATION);
                $statement->bindValue('user_id', $currentUser->getId());
                $statement->bindValue('available_goods_reservation_id', $availableGoodsReservationId);
                $statement->execute();
            }

            // Добавляем запись в регистр потребностей
            $sql = '
                INSERT INTO goods_need_register ( base_product_id, delta, order_item_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                SELECT
                    oi.base_product_id,
                    -:quantity :: integer,
                    oi.id,
                    agr.id,
                    :available_goods_reservation,
                    agr.created_at,
                    :available_goods_reservation_operation,
                    now( ),
                    :user_id::INTEGER 
                FROM
                    available_goods_reservation AS agr
                    JOIN order_item AS oi ON oi.id = :order_item_id 
                WHERE
                    agr.id = :available_goods_reservation_id
            ';

            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('quantity', $sumReservingQuantities);
            $statement->bindValue('available_goods_reservation', DocumentTypeCode::AVAILABLE_GOODS_RESERVATION);
            $statement->bindValue('available_goods_reservation_operation', OperationTypeCode::AVAILABLE_GOODS_RESERVATION);
            $statement->bindValue('user_id', $currentUser->getId());
            $statement->bindValue('order_item_id', $command->id);
            $statement->bindValue('available_goods_reservation_id', $availableGoodsReservationId);
            $statement->execute();

            if ($supplierQuantity > 0 && $sumReservingQuantities > $processingQuantity) {
                $query = $em->createNativeQuery("
                    SELECT
                        gnr.base_product_id,
                        SUM( gnr.delta ) AS quantity,
                        gnr.order_item_id,
                        gnr.supplier_reserve_id,
                        gnr.supply_id,
                        gnr.purchase_price 
                    FROM
                        supplier_reserve_register AS gnr
                        JOIN supplier_reserve AS sr ON sr.supplier_id = gnr.supplier_id 
                        AND sr.closed_at IS NULL 
                    WHERE
                        gnr.order_item_id = :order_item_id 
                    GROUP BY
                        gnr.base_product_id,
                        gnr.order_item_id,
                        gnr.supplier_reserve_id,
                        gnr.supply_id,
                        gnr.purchase_price 
                    HAVING
                        SUM( gnr.delta ) > 0
                ", new ResultSetMapping());
                $query->setParameter('order_item_id', $command->id);

                $shippingReserves = $query->getResult('ListAssocHydrator');

                $writeOff = [];
                $quantity = $sumReservingQuantities - $processingQuantity - $supplierQuantity;
                foreach ($shippingReserves as $shippingReserve) {
                    if ($shippingReserve['quantity'] <= $quantity) {
                        $writeOff[] = $shippingReserve;
                        $quantity -= $shippingReserve['quantity'];

                        if ($quantity == 0) {
                            break;
                        }
                    } else {
                        $shippingReserve['quantity'] = $quantity;
                        $writeOff[] = $shippingReserve;
                        break;
                    }
                }

                if ($writeOff) {
                    $values = [];
                    foreach ($writeOff as $value) {
                        $values[] = sprintf('(%u::INTEGER, %u::INTEGER, %u::INTEGER, %u::INTEGER)',
                            $value['base_product_id'],
                            $value['quantity'],
                            $value['order_item_id'],
                            $value['supplier_reserve_id']
                        );
                    }

                    $sql = '
                        WITH DATA ( base_product_id, quantity, order_item_id, supplier_reserve_id ) AS 
                        ( VALUES '.implode(',', $values).' ) 
                        INSERT INTO goods_need_register ( base_product_id, delta, order_item_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                        SELECT
                            base_product_id,
                            quantity,
                            order_item_id,
                            supplier_reserve_id,
                            :supplier_reserve,
                            ( SELECT created_at FROM supplier_reserve WHERE id = supplier_reserve_id ),
                            :available_goods_reservation,
                            now( ),
                            :user_id::INTEGER 
                        FROM
                            DATA                
                    ';

                    $statement = $em->getConnection()->prepare($sql);
                    $statement->bindValue('available_goods_reservation', DocumentTypeCode::AVAILABLE_GOODS_RESERVATION);
                    $statement->bindValue('supplier_reserve', GoodsNeedRegisterType::SUPPLIER_RESERVE);
                    $statement->bindValue('user_id', $currentUser->getId());
                    $statement->execute();

                    $values = [];
                    foreach ($writeOff as $value) {
                        $values[] = sprintf('(%u::INTEGER, %u::INTEGER, %u::INTEGER, %u::INTEGER, %u::INTEGER, %u::INTEGER)',
                            $value['base_product_id'],
                            $value['quantity'],
                            $value['order_item_id'],
                            $value['supplier_reserve_id'],
                            $value['supply_id'],
                            $value['purchase_price']
                        );
                    }

                    $sql = '
                        WITH DATA ( base_product_id, quantity, order_item_id, supplier_reserve_id, supply_id, purchase_price ) AS 
                        ( VALUES '.implode(',', $values).' ) 
                        INSERT INTO supplier_reserve_register ( base_product_id, delta, order_item_id, supplier_reserve_id, supply_id, supplier_id, purchase_price, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                        SELECT
                            d.base_product_id,
                            - d.quantity,
                            oi.id,
                            d.supplier_reserve_id,
                            d.supply_id,
                            sr.supplier_id,
                            d.purchase_price,
                            d.supplier_reserve_id,
                            :supplier_reserve,
                            sr.created_at,
                            :supplier_reservation,
                            now( ),
                            :user_id::INTEGER 
                        FROM
                            DATA AS d
                            JOIN order_item AS oi ON oi.id = :order_item_id
                            JOIN supplier_reserve AS sr ON sr.id = d.supplier_reserve_id                
                    ';

                    $statement = $em->getConnection()->prepare($sql);
                    $statement->bindValue('supplier_reserve', GoodsNeedRegisterType::SUPPLIER_RESERVE);
                    $statement->bindValue('supplier_reservation', DocumentTypeCode::SUPPLIER_RESERVATION);
                    $statement->bindValue('user_id', $currentUser->getId());
                    $statement->bindValue('order_item_id', $command->id);
                    $statement->execute();
                }
            }

            $em->getConnection()->commit();
        } catch (\Exception $ex) {

            $em->getConnection()->rollback();

            throw $ex;
        }
    }
}