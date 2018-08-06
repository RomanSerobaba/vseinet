<?php

namespace ServiceBundle\Services;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Enum\GoodsNeedRegisterType;
use AppBundle\Enum\GoodsReserveOperationCode;
use AppBundle\Enum\GoodsReserveType;
use AppBundle\Enum\OperationTypeCode;
use AppBundle\Enum\OrderCommentType;
use AppBundle\Enum\SupplierReserveStatus;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\ResultSetMapping;
use OrderBundle\Entity\OrderComment;
use Doctrine\ORM\EntityManager;
use OrderBundle\Entity\OrderItem;
use SupplyBundle\Component\OrderComponent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\ORM\Query\DTORSM;

class OrderService extends MessageHandler
{
    /**
     * Просмотр комментариев по заказу
     *
     * @param int $orderId
     *
     * @return array
     */
    public function getComments(int $orderId) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();
        
        $query = $em->createNativeQuery('
            SELECT
                order_comment.order_item_id,
                order_comment.text,
                order_comment.created_by,
                order_comment.created_at,
                to_char(order_comment.created_at, \'YYYY-MM-DD"T"HH24:MI:SS"+03:00"\') as created_at,
                order_comment.type,
                order_comment.is_important,
                CASE 
                    WHEN order_comment.order_item_id IS NULL 
                    THEN TRUE 
                    ELSE FALSE 
                END AS is_common,
                CASE 
                    WHEN order_comment.type = :client THEN \'Клиент\' 
                    WHEN order_comment.type = :franchiser THEN	\'Франчайзер\' 
                    ELSE vup.fullname 
                END AS commentator 
            FROM
                order_comment
                LEFT JOIN func_view_user_person(order_comment.created_by) AS vup ON vup.id = order_comment.created_by 
            WHERE
                order_comment.order_id = :order_id 
            ORDER BY
                order_comment.created_at
        ', new ResultSetMapping());
        $query->setParameter('order_id', $orderId);
        $query->setParameter('client', OrderComment::TYPE_CLIENT);
        $query->setParameter('franchiser', OrderComment::TYPE_FRANCHISER);

        return $query->getResult('ListAssocHydrator');
    }

    /**
     * Просмотр комментариев по заказу
     *
     * @param int  $orderItemId
     * @param bool $withOrderComments
     *
     * @return array
     */
    public function getOrderItemComments(int $orderItemId, bool $withOrderComments = false) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createNativeQuery('
            SELECT
                oc.id,
                oc.order_item_id,
                oc.text,
                oc.created_by,
                oc.created_at,
                oc.type,
                oc.is_important,
                CASE WHEN oc.order_item_id IS NULL 
                    THEN TRUE 
                    ELSE FALSE 
                END AS is_common,
                CASE WHEN oc.type = :client THEN \'Клиент\' 
                    WHEN oc.type = :franchiser THEN \'Франчайзер\' 
                    ELSE vup.fullname 
                END AS commentator 
            FROM
                order_item AS oi
                JOIN order_comment AS oc ON ( oc.order_item_id = oi.id '.($withOrderComments ? 'OR oc.order_id = oi.order_id AND oc.order_item_id IS NULL' : '').' )
                LEFT JOIN func_view_user_person(oc.created_by) AS vup ON vup.id = oc.created_by 
            WHERE
                oi.id = :order_item_id 
            ORDER BY
                oc.created_at
        ', new DTORSM(\OrderBundle\Bus\Item\Query\DTO\GetComments::class));
        $query->setParameter('order_item_id', $orderItemId);
        $query->setParameter('client', OrderComment::TYPE_CLIENT);
        $query->setParameter('franchiser', OrderComment::TYPE_FRANCHISER);

        return $query->getResult('DTOHydrator');
    }

    /**
     * Редактирование количества позиции заказа
     *
     * @param int $orderItemId
     * @param int $newQuantity
     *
     * @return int
     * @throws \Exception
     */
    public function changeItemQuantity(int $orderItemId, int $newQuantity) : int
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        if ($newQuantity <= 0) {
            throw new BadRequestHttpException('Недопустимое количество');
        }

        $query = $em->createNativeQuery('
            SELECT 
                SUM ( gnr.delta ) != oi.quantity AS is_processed 
            FROM
                goods_need_register AS gnr
                JOIN order_item AS oi ON oi.id = gnr.order_item_id 
            WHERE
                gnr.order_item_id = :order_item_id 
            GROUP BY
                oi.quantity
        ', new ResultSetMapping());
        $query->setParameter('order_item_id', $orderItemId);

        $rows = $query->getResult('ListAssocHydrator');
        $row = array_shift($rows);
        $isProcessed = $row['is_processed'];

        if (empty($isProcessed)) {
            $em->getConnection()->beginTransaction();
            try {
                $query = $em->createNativeQuery('
                    UPDATE order_item 
                    SET quantity = :new_quantity 
                    WHERE id = :order_item_id
                ', new ResultSetMapping());
                $query->setParameter('new_quantity', $newQuantity);
                $query->setParameter('order_item_id', $orderItemId);
                $query->execute();

                /**
                 * @var RegisterService $service
                 */
                $service = $this->get('service.register');
                $service->orderItem([$orderItemId,]);

                $em->getConnection()->commit();
            } catch (\Exception $ex) {
                $em->getConnection()->rollback();

                throw $ex;
            }
        }

        return $orderItemId;
    }

    /**
     * Редактирование количества заявки
     *
     * @param int $goodsRequestId
     * @param int $newQuantity
     *
     * @return int
     */
    public function changeRequestQuantity(int $goodsRequestId, int $newQuantity) : int
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        if ($newQuantity <= 0) {
            throw new BadRequestHttpException('Недопустимое количество');
        }

        $query = $em->createNativeQuery('
            SELECT
                (SUM(gnr.delta) != gr.quantity)::int AS is_processed 
            FROM
                goods_need_register AS gnr
                JOIN goods_request AS gr ON gr.id = gnr.goods_request_id 
            WHERE
                gnr.goods_request_id = :goods_request_id 
            GROUP BY
                gr.quantity
        ', new ResultSetMapping());
        $query->setParameter('goods_request_id', $goodsRequestId);

        $isProcessed = $query->getSingleScalarResult();

        if (empty($isProcessed)) {
            $query = $em->createNativeQuery('
                UPDATE goods_request 
                SET quantity = :new_quantity 
                WHERE id = :goods_request_id
            ', new ResultSetMapping());
            $query->setParameter('new_quantity', $newQuantity);
            $query->setParameter('goods_request_id', $goodsRequestId);
            $query->execute();

            $service = $this->get('service.register');
            $service->goodsRequest([$goodsRequestId,]);
        }

        return $goodsRequestId;
    }

    /**
     * Сброс резерва позиции заказа
     *
     * @param int $orderItemId
     * @param int $quantity
     */
    public function resetItemReserve(int $orderItemId, int $quantity = 0) : void
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $orderItem = $em->getRepository(OrderItem::class)->find($orderItemId);
        if (!$orderItem instanceof OrderItem) {
            throw new NotFoundHttpException(sprintf('Позиция заказа %u не найдена', $orderItemId));
        }

        $query = $em->createNativeQuery('
            SELECT
                supply_item_id,
                geo_room_id,
                shipment_id,
                SUM(delta) AS quantity 
            FROM
                goods_reserve_log 
            WHERE
                order_item_id = :order_item_id 
                AND reserve_type = :new 
            GROUP BY
                shipment_id,
                geo_room_id,
                supply_item_id 
            HAVING
                SUM(delta) > 0
        ', new ResultSetMapping());
        $query->setParameter('order_item_id', $orderItemId);
        $query->setParameter('new', GoodsReserveType::NEW);

        $items = $query->getResult('ListAssocHydrator');
        $reserveService = $this->get('service.reserve');
        $currentQuantity = $quantity;

        foreach ($items as $item) {
            if ($quantity > 0) {
                if ($currentQuantity >= $item['quantity']) {
                    $delta = $item['quantity'];
                    $currentQuantity -= $delta;
                } else {
                    $delta = $quantity;
                    $currentQuantity = 0;
                }
            } else {
                $delta = $item['quantity'];
            }

            $reserveService->change(
                $orderItem->getBaseProductId(),
                $item['supply_item_id'],
                DocumentTypeCode::ORDER_ITEM,
                $orderItemId,
                GoodsReserveOperationCode::ANNUL_RESERVE,
                GoodsReserveType::NEW,
                $item['geo_room_id'],
                -$delta,
                null,
                $orderItemId,
                null,null,null,
                $item['shipment_id']
            );

            $reserveService->change(
                $orderItem->getBaseProductId(),
                $item['supply_item_id'],
                DocumentTypeCode::ORDER_ITEM,
                $orderItemId,
                GoodsReserveOperationCode::ANNUL_RESERVE,
                GoodsReserveType::NEW,
                $item['geo_room_id'],
                $delta,
                null,
                null,
                null,null,null,
                $item['shipment_id']
            );

            if ($quantity > 0 && $currentQuantity == 0) {
                break;
            }
        }
    }

    /**
     * Получение текущих резервов
     *
     * @param int $orderItemId
     *
     * @return array
     */
    public function getItemReserves(int $orderItemId) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createNativeQuery('
            SELECT
                shipment_id,
                supply_item_id,
                geo_room_id,
                reserve_type,
                SUM(delta) AS amount 
            FROM
                goods_reserve_log 
            WHERE
                order_item_id = :order_item_id 
            GROUP BY
                shipment_id,
                supply_item_id,
                geo_room_id,
                reserve_type 
            HAVING
                SUM(delta) > 0
        ', new ResultSetMapping());
        $query->setParameter('order_item_id', $orderItemId);

        return $query->getResult('ListAssocHydrator');
    }

    /**
     * Аннулирование позиции
     *
     * @param int    $orderItemId
     * @param int    $quantity
     * @param string $causeCode
     * @param string $comment
     * @param bool   $isClientOffender
     * @param bool   $isReserveCanceled
     *
     * @return int
     */
    public function annulItem(int $orderItemId, int $quantity, string $causeCode, string $comment, bool $isClientOffender = false, bool $isReserveCanceled = false) : int
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

        $query = $em->createNativeQuery('
            INSERT INTO order_annul ( order_annul_cause_code, comment, created_at, created_by, approved_at, approved_by, is_reserve_canceled, is_client_offender )
            VALUES ( :cause_code, :comment, NOW(), :user_id::INTEGER, NOW(), :user_id::INTEGER, :is_reserve_canceled, :is_client_offender ) RETURNING id 
        ', new ResultSetMapping());
        $query->setParameter('cause_code', $causeCode);
        $query->setParameter('comment', $comment);
        $query->setParameter('user_id', $currentUserId);
        $query->setParameter('is_reserve_canceled', $isReserveCanceled);
        $query->setParameter('is_client_offender', $isClientOffender);

        $orderAnnulId = $query->execute();

        if (!empty($orderAnnulId)) {
            $query = $em->createNativeQuery('
                INSERT INTO order_annul_item ( order_item_id, order_annul_id, quantity )
                VALUES ( :order_item_id, :order_annul_id, :quantity )', new ResultSetMapping());
            $query->setParameter('order_item_id', $orderItemId);
            $query->setParameter('order_annul_id', $orderAnnulId);
            $query->setParameter('quantity', $quantity);

            $query->execute();

            $query = $em->createNativeQuery('
                INSERT INTO goods_need_register ( delta, order_item_id, goods_request_id, type, registrator_id, registered_at, register_operation_type_code, registrator_type_code, base_product_id, created_at, created_by ) 
                SELECT
                    :quantity AS delta,
                    oi.id AS order_item_id,
                    NULL AS goods_request_id,
                    :annul AS type,
                    :order_annul_id AS registrator_id,
                    NOW() AS registered_at,
                    :order_annul AS register_operation_type_code,
                    :order_annul AS registrator_type_code,
                    oi.base_product_id,
                    NOW() AS created_at,
                    :user_id::INTEGER AS created_by 
                FROM
                    order_item AS oi 
                WHERE
                    oi.id = :order_item_id
            ', new ResultSetMapping());

            $query->setParameter('register_operation_type_code_order_item_annul', OperationTypeCode::GOODS_REQUEST_ANNUL);
            $query->setParameter('registrator_type_code_order_item_annul', DocumentTypeCode::GOODS_REQUEST_ANNUL);
            $query->setParameter('order_annul_id', $orderAnnulId);
            $query->setParameter('annul', GoodsNeedRegisterType::ANNUL);
            $query->setParameter('quantity', $quantity);
            $query->setParameter('order_item_id', $orderItemId);
            $query->setParameter('user_id', $currentUserId);
            $query->execute();

            return $orderAnnulId;
        } else {
            throw new BadRequestHttpException('Ошибка создания записи в таблице order_annul');
        }
    }

    /**
     * Аннулирование заявки
     *
     * @param int $goodsRequestId
     * @param int $quantity
     *
     * @return int
     */
    public function annulRequest(int $goodsRequestId, int $quantity) : int
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

        $query = $em->createNativeQuery('
            INSERT INTO goods_request_annul (created_at, created_by, goods_request_id, quantity)
            VALUES (NOW(), :user_id::INTEGER, :goods_request_id::INTEGER, :quantity) RETURNING id
        ', new ResultSetMapping());
        $query->setParameter('user_id', $currentUserId);
        $query->setParameter('goods_request_id', $goodsRequestId);
        $query->setParameter('quantity', $quantity);

        $goodsRequestAnnulId = $query->execute();

        if ($goodsRequestAnnulId) {
            $query = $em->createNativeQuery('
                INSERT INTO goods_need_register (delta, order_item_id, goods_request_id, type, registrator_id, registered_at, register_operation_type_code, registrator_type_code, base_product_id, created_at, created_by ) 
                SELECT
                    :quantity AS delta,
                    NULL AS order_item_id,
                    gr.id AS goods_request_id,
                    :annul AS type,
                    :goods_request_annul_Id AS registrator_id,
                    NOW() AS registered_at,
                    :register_operation_type_code_goods_request_annul AS register_operation_type_code,
                    :registrator_type_code_goods_request_annul AS registrator_type_code,
                    gr.base_product_id,
                    NOW() AS created_at,
                    :user_id::INTEGER AS created_by 
                FROM
                    goods_request AS gr 
                WHERE
                    gr.id = :goods_request_id
            ', new ResultSetMapping());

            $query->setParameter('quantity', $quantity);
            $query->setParameter('annul', GoodsNeedRegisterType::ANNUL);
            $query->setParameter('goods_request_annul_id', $goodsRequestAnnulId);
            $query->setParameter('register_operation_type_code_goods_request_annul', OperationTypeCode::GOODS_REQUEST_ANNUL);
            $query->setParameter('registrator_type_code_goods_request_annul', DocumentTypeCode::GOODS_REQUEST_ANNUL);
            $query->setParameter('user_id', $currentUserId);

            $query->execute();

            return $goodsRequestAnnulId;
        } else {
            throw new BadRequestHttpException('Ошибка создания записи в таблице goods_request_annul');
        }
    }

    /**
     * Смена поставщика для обработки
     *
     * @param int $id
     * @param int $newSupplierId
     */
    public function changeItemSupplier(int $id, int $newSupplierId) : void
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createNativeQuery('
            INSERT INTO order_item_to_supplier (supplier_id, order_item_id, is_delayed) 
            SELECT
                supplier_id,
                id,
                TRUE 
            FROM
                order_item 
            WHERE
                id = :id 
                AND supplier_id != :new_supplier_id 
            ON duplicate KEY UPDATE is_delayed = TRUE
        ', new ResultSetMapping());
        $query->setParameter('id', $id);
        $query->setParameter('new_supplier_id', $newSupplierId);
        $query->execute();

        $query = $em->createNativeQuery('
            UPDATE order_item 
                SET supplier_id = :new_supplier_id,
                supplier_reserve = :processing 
            WHERE
                supplier_reserve IS NOT NULL 
                AND id = :id 
                AND supplier_id != :new_supplier_id
        ', new ResultSetMapping());
        $query->setParameter('id', $id);
        $query->setParameter('new_supplier_id', $newSupplierId);
        $query->setParameter('processing', SupplierReserveStatus::PROCESSING);
        $query->execute();
    }

    // Дублирование позиции
    public function cloneItem(int $orderItemId, int $newQuantity)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository(OrderItem::class);
        /**
         * @var OrderItem $model
         */
        $model = $repository->find($orderItemId);

        if (!$model instanceof OrderItem) {
            throw new NotFoundHttpException('Позиция не найден');
        }

        $newModel = clone $model;
        $newModel->setId(null);
        $newModel->setQuantity($newQuantity);

        $em->persist($newModel);
        $em->flush();

        return $newModel->getId();
    }

    /**
     * Получение позиций по фильтру
     *
     * @param string $statusCode
     * @param int    $supplierId
     * @param bool   $withOtherActiveItems
     *
     * @return array
     */
    public function getItems(string $statusCode, int $supplierId, bool $withOtherActiveItems = false) : array
    {
        return [];
    }

        /**
     * Добавление позиции к заказу
     *
     * @param int $orderId
     * @param int $baseProductId
     * @param int $quantity
     *
     * @return int
     */
    public function addItem(int $orderId, int $baseProductId, int $quantity) : int
    {
        return 1;
    }

    /**
     * Резервирование позиции
     *
     * @param int   $orderItemId
     * @param array $quantities [['geo_point_id' => 1, 'quantity' => 7, 'is_in_transit' => false], ...]
     * @param null  $supplierReserveId
     *
     * @param bool  $isGet
     *
     * @return array
     */
    public function reserveItem(int $orderItemId, array $quantities, $supplierReserveId = null, bool $isGet = false): array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $orderItem = $em->getRepository(OrderItem::class)->find($orderItemId);

        if (!$orderItem) {
            throw new NotFoundHttpException('Order item not found by ID '.$orderItemId);
        }

        $supplierQuantity = $resetQuantity = $availableGoodsReservationId = $reservedQuantity = 0;

        $sql = 'SELECT SUM(delta) AS quantity 
            FROM
                goods_need_register 
            WHERE
                order_item_id = :order_item_id
        ';

        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('order_item_id', $orderItemId);
        $statement->execute();

        $needQuantity = $statement->fetchColumn();

        if (!empty($supplierReserveId)) {
            $sql = '
                SELECT
                    SUM( - delta ) AS quantity 
                FROM
                    goods_need_register 
                WHERE
                    order_item_id = :order_item_id 
                    AND supplier_reserve_id = :supplier_reserve_id 
                    AND TYPE = :supplier_reserve
            ';

            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('order_item_id', $orderItemId);
            $statement->bindValue('supplier_reserve_id', $supplierReserveId);
            $statement->bindValue('supplier_reserve', GoodsNeedRegisterType::SUPPLIER_RESERVE);
            $statement->execute();

            $supplierQuantity = $statement->fetchColumn();
            $needQuantity += $supplierQuantity;
        }

        foreach ($quantities as $row) {
            $geoPointID = $row['geo_point_id'];
            $quantity = $row['quantity'];
            $isInTransit = (bool) $row['is_in_transit'];

            $query = $em->createNativeQuery('
                SELECT
                    r.quantity AS reserve_quantity,
                    r.goods_request_id,
                    r.goods_release_id,
                    r.supply_item_id,
                    r.supplier_invoice_item_id 
                FROM (
                    SELECT
                        geo_room_id,
                        goods_release_id,
                        goods_request_id,
                        supply_item_id,
                        supplier_invoice_item_id,
                        SUM ( delta ) AS quantity 
                    FROM
                        goods_reserve_register 
                    WHERE
                        base_product_id = :base_product_id 
                        AND goods_condition_code = :free 
                    GROUP BY
                        supply_item_id,
                        supplier_invoice_item_id,
                        goods_release_id,
                        geo_room_id,
                        goods_request_id 
                    HAVING
                        SUM ( delta ) > 0 
                ) AS r
                    LEFT JOIN geo_room AS gr ON gr.id = r.geo_room_id
                    LEFT JOIN supplier_invoice_item AS sii ON sii.id = r.supplier_invoice_item_id
                    LEFT JOIN supplier_invoice AS si ON si.id = sii.supplier_invoice_id
                    LEFT JOIN goods_release_doc AS gre ON gre.number = r.goods_release_id
                    JOIN view_geo_point AS vgp ON vgp.id = COALESCE ( gr.geo_point_id, si.destination_point_id, gre.destination_point_id ) 
                WHERE
                    vgp.id = :geo_point_id 
                    AND CASE WHEN r.geo_room_id IS NULL THEN TRUE ELSE FALSE END = :is_in_transit 
            ', new ResultSetMapping());
            $query->setParameter('base_product_id', $orderItem->getBaseProductId());
            $query->setParameter('free', GoodsConditionCode::FREE);
            $query->setParameter('geo_point_id', $geoPointID);
            $query->setParameter('is_in_transit', $isInTransit);

            $reserves = $query->getResult('ListAssocHydrator');

            foreach ($reserves as $reserve) {
                if ($quantity > $needQuantity) {
                    $quantity = $needQuantity;
                }

                if ($reserve['reserve_quantity'] > $quantity) {
                    $reserve['reserve_quantity'] = $quantity;
                }

                $reservedQuantity += $reserve['reserve_quantity'];

                if ($reserve['reserve_quantity'] > 0 && !$isGet) {
                    if (empty($availableGoodsReservationId)) {
                        $sql = '
                            INSERT INTO available_goods_reservation ( created_at, created_by )
                            VALUES ( NOW(), :user_id::INTEGER ) RETURNING id
                        ';

                        $statement = $em->getConnection()->prepare($sql);
                        $statement->bindValue('user_id', $currentUser->getId());
                        $statement->execute();

                        $availableGoodsReservationId = $statement->fetchColumn();
                    }

                    $sql = '
                        WITH DATA (available_goods_reservation_id, order_item_id, goods_request_id, delta, geo_room_id, supplier_invoice_item_id, goods_release_id, supply_item_id ) 
                        AS ( VALUES (:available_goods_reservation_id, NULL, :goods_request_id, -:reserve_quantity, :geo_room_id, :supplier_invoice_item_id, :goods_release_id, :supply_item_id ),
                                    (:available_goods_reservation_id, :order_item_id, NULL, :reserve_quantity, :geo_room_id, :supplier_invoice_item_id, :goods_release_id, :supply_item_id)
                        ) INSERT INTO available_goods_reservation_item (available_goods_reservation_id, order_item_id, goods_request_id, delta, geo_room_id, supplier_invoice_item_id, goods_release_id, supply_item_id)
                        SELECT * FROM DATA
                    ';

                    $statement = $em->getConnection()->prepare($sql);
                    $statement->bindValue('available_goods_reservation_id', $availableGoodsReservationId);
                    $statement->bindValue('order_item_id', $orderItemId);
                    $statement->bindValue('goods_request_id', $reserve['goods_request_id']);
                    $statement->bindValue('reserve_quantity', $reserve['reserve_quantity']);
                    $statement->bindValue('geo_room_id', $reserve['geo_room_id']);
                    $statement->bindValue('supplier_invoice_item_id', $reserve['supplier_invoice_item_id']);
                    $statement->bindValue('goods_release_id', $reserve['goods_release_id']);
                    $statement->bindValue('supply_item_id', $reserve['supply_item_id']);
                    $statement->execute();

                    if ($reserve['goods_request_id']) {
                        $sql = '
                            UPDATE goods_request 
                            SET quantity = quantity - :reserve_quantity 
                            WHERE
                                id = :goods_request_id
                        ';

                        $statement = $em->getConnection()->prepare($sql);
                        $statement->bindValue('reserve_quantity', $reserve['reserve_quantity']);
                        $statement->bindValue('goods_request_id', $reserve['goods_request_id']);
                        $statement->execute();
                    }
                }

                if ($reserve['reserve_quantity'] == $quantity) {
                    break;
                }
            }

            if ($quantity == $needQuantity) {
                break;
            }
        }

        $resetQuantity = $supplierQuantity - $needQuantity + $reservedQuantity;

        if (!empty($availableGoodsReservationId)) {
            /**
             * @var RegisterService $registerService
             */
            $registerService = $this->get('service.register');
            $registerService->availableGoodsReserve([$availableGoodsReservationId,]);

            if (!empty($supplierReserveId) && $resetQuantity > 0) {
                $component = new OrderComponent($this->getEm());
                $component->supplierGoodsReservation($registerService, $currentUser, $supplierReserveId, [[$orderItemId => $resetQuantity,],], [], $availableGoodsReservationId, DocumentTypeCode::AVAILABLE_GOODS_RESERVATION);
            }
        }

        $processingQuantity = $needQuantity - $supplierQuantity - $reservedQuantity;

        return ['processing' => $processingQuantity > 0 ? $processingQuantity : 0, 'reserved' => $supplierQuantity - $resetQuantity,];
    }

    /**
     * @param int            $orderItemId
     * @param array          $list
     * @param array          $orderItem
     * @param int            $reservingQuantity
     * @param ReserveService $serviceReserve
     *
     * @return int
     */
    private function _processReserveItems(int $orderItemId, array $list, array $orderItem, int $reservingQuantity, ReserveService $serviceReserve)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        foreach ($list as $id => $rows) {
            $currentQuantity = 0;
            foreach ($rows as $row) {
                $currentQuantity += $row['quantity'];
            }

            if ($currentQuantity < $reservingQuantity) {
                $query = $em->createNativeQuery('
                    UPDATE order_item 
                    SET quantity = quantity - :current_quantity 
                    WHERE
                        id = :order_item_id
                ', new ResultSetMapping());
                $query->setParameter('current_quantity', $currentQuantity);
                $query->setParameter('order_item_id', $orderItemId);
                $query->execute();

                $newItemId = $this->cloneItem($orderItemId, $currentQuantity);
            } else {
                $newItemId = $orderItemId;
            }

            $reservingQuantity -= $currentQuantity;

            foreach ($rows as $row) {
                $serviceReserve->change(
                    $orderItem['base_product_id'],
                    $row['supply_item_id'],
                    DocumentTypeCode::ORDER_ITEM,
                    $orderItemId,
                    GoodsReserveOperationCode::RESERVE,
                    GoodsReserveType::NEW,
                    $row['geo_room_id'],
                    -$row['quantity'],
                    null,
                    $row['goods_request_id'],
                    null, null, null,
                    $row['shipment_id']
                );
                $serviceReserve->change(
                    $orderItem['base_product_id'],
                    $row['supply_item_id'],
                    DocumentTypeCode::ORDER_ITEM,
                    $orderItemId,
                    GoodsReserveOperationCode::RESERVE,
                    GoodsReserveType::NEW,
                    $row['geo_room_id'],
                    $row['quantity'],
                    null,
                    $orderItemId,
                    null, null, null,
                    $row['shipment_id']
                );
            }

            $orderItemId = $newItemId;
        }

        return $reservingQuantity;
    }

    /**
     * Добавление комментария
     *
     * @param int    $orderId
     * @param string $text
     * @param null   $orderItemId
     * @param bool   $isImportant
     *
     * @return int
     */
    public function addComment(int $orderId, string $text, $orderItemId = null, $isImportant = false) : int
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var \AppBundle\Entity\User $user
         */
        $user = $this->get('user.identity')->getUser();

        $statement = $em->getConnection()->prepare('
            INSERT INTO order_comment ( order_id, order_item_id, text, created_by, created_at, type, is_important )
            VALUES (
                :order_id,
                :order_item_id,
                :text,
                :user_id::INTEGER,
                NOW(),
                COALESCE ((SELECT CASE WHEN acl_subrole_id = 18 THEN :franchiser ELSE :manager END FROM user_to_acl_subrole WHERE user_id = :user_id LIMIT 1 ), :client)::order_comment_type,
                :is_important 
            )
            RETURNING id
        ');
        $statement->bindValue('order_id', $orderId);
        $statement->bindValue('order_item_id', $orderItemId);
        $statement->bindValue('text', $text);
        $statement->bindValue('user_id', $user->getId());
        $statement->bindValue('franchiser', OrderCommentType::FRANCHISER);
        $statement->bindValue('manager', OrderCommentType::MANAGER);
        $statement->bindValue('client', OrderCommentType::CLIENT);
        $statement->bindValue('is_important', $isImportant, Type::BOOLEAN);
        $statement->execute();

        return $statement->fetchColumn();
    }
}
