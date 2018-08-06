<?php

namespace ServiceBundle\Services;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Enum\GoodsNeedRegisterType;
use AppBundle\Enum\OperationTypeCode;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;

class RegisterService extends MessageHandler
{
    /**
     * @param array  $registratorIds
     * @param string $registratorTypeCode
     */
    public function purge(array $registratorIds, string $registratorTypeCode) : void
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createNativeQuery('
            DELETE FROM
                goods_need_register 
            WHERE
                registrator_id = ( SELECT order_id FROM order_item WHERE id IN (:registrator_ids) ) 
                AND registrator_type_code = :registrator_type_code
        ', new ResultSetMapping());
        $query->setParameter('registrator_ids', $registratorIds);
        $query->setParameter('registrator_type_code', $registratorTypeCode);

        $query->execute();

//        $query = $em->createNativeQuery('
//            DELETE FROM
//                goods_reserve_register
//            WHERE
//                registrator_id IN (:registrator_ids)
//                AND registrator_type_code = :registrator_type_code
//        ', new ResultSetMapping());
//        $query->setParameter('registrator_ids', $registratorIds);
//        $query->setParameter('registrator_type_code', $registratorTypeCode);
//
//        $query->execute();
    }

    /**
     * Перепроводка резерва поставщика
     *
     * @param array $supplierReserveItemIds
     */
    public function supplierReserveItem(array $supplierReserveItemIds) : void
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $this->purge($supplierReserveItemIds,  DocumentTypeCode::SUPPLIER_RESERVE_ITEM);

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $query = $em->createNativeQuery('
            INSERT INTO goods_need_register ( delta, order_item_id, type, registrator_id, registered_at, register_operation_type_code, registrator_type_code, base_product_id, created_at, created_by ) 
            SELECT
                - sri.quantity,
                sri.order_item_id,
                :supplier_reserve,
                sri.id,
                sr.created_at,
                :supplier_goods_reservation,
                :supplier_reserve_item,
                sri.base_product_id,
                NOW(),  
                :user_id::INTEGER 
            FROM
                supplier_reserve_item sri
                JOIN supplier_reserve AS sr ON sr.id = sri.supplier_reserve_id 
            WHERE
                sri.id IN (:supplier_reserve_ids) 
                AND sri.quantity > 0
        ', new ResultSetMapping());
        $query->setParameter('supplier_reserve', GoodsNeedRegisterType::SUPPLIER_RESERVE);
        $query->setParameter('supplier_goods_reservation', OperationTypeCode::SUPPLIER_GOODS_RESERVATION);
        $query->setParameter('supplier_reserve_item', DocumentTypeCode::SUPPLIER_RESERVE_ITEM);
        $query->setParameter('user_id', $currentUser->getId());
        $query->setParameter('supplier_reserve_ids', $supplierReserveItemIds);

        $query->execute();
    }

    /**
     * @param array $orderItemIds
     */
    public function orderItem(array $orderItemIds) : void
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $this->purge($orderItemIds, DocumentTypeCode::ORDER);

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $query = $em->createNativeQuery('
            INSERT INTO goods_need_register ( delta, order_item_id, registrator_id, registered_at, register_operation_type_code, registrator_type_code, base_product_id, created_at, created_by ) 
            SELECT
                oi.quantity AS delta,
                oi.id AS order_item_id,
                oi.order_id AS registrator_id,
                o.created_at AS registered_at,
                :order_creation AS register_operation_type_code,
                :order AS registrator_type_code,
                oi.base_product_id,
                NOW() AS created_at, 
                :user_id::INTEGER AS created_by 
            FROM
                order_item AS oi
                JOIN "order" AS o ON o.id = oi.order_id 
            WHERE
                o.id = (SELECT order_id FROM order_item WHERE id IN (:order_item_ids))
        ', new ResultSetMapping());
        $query->setParameter('order_creation', OperationTypeCode::ORDER_CREATION);
        $query->setParameter('order', DocumentTypeCode::ORDER);
        $query->setParameter('user_id', $currentUser->getId());
        $query->setParameter('order_item_ids', $orderItemIds);

        $query->execute();
    }

    /**
     * @param array $goodsRequestIds
     */
    public function goodsRequest(array $goodsRequestIds) : void
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $this->purge($goodsRequestIds, DocumentTypeCode::GOODS_REQUEST);

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $query = $em->createNativeQuery('
            INSERT INTO goods_need_register ( delta, order_item_id, goods_request_id, type, registrator_id, registered_at, register_operation_type_code, registrator_type_code, base_product_id, created_at, created_by ) 
            SELECT
                gr.quantity AS delta,
                NULL AS order_item_id,
                gr.goods_request_id AS goods_request_id,
                :need AS type,
                gr.id AS registrator_id,
                gr.created_at AS registered_at,
                :goods_request_creation AS register_operation_type_code,
                :goods_request AS registrator_type_code,
                gr.base_product_id,
                NOW() AS created_at,
                :user_id::INTEGER AS created_by 
            FROM
                goods_request AS gr 
            WHERE
                gr.id IN (:goods_request_ids)
        ', new ResultSetMapping());
        $query->setParameter('need', GoodsNeedRegisterType::NEED);
        $query->setParameter('goods_request_creation', OperationTypeCode::GOODS_REQUEST_CREATION);
        $query->setParameter('goods_request', DocumentTypeCode::GOODS_REQUEST);
        $query->setParameter('user_id', $currentUser->getId());
        $query->setParameter('goods_request_ids', $goodsRequestIds);

        $query->execute();
    }

    /**
     * @param array $supplierGoodsReservationIds
     */
    public function supplierGoodsReservation(array $supplierGoodsReservationIds) : void
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $query = $em->createNativeQuery('
            INSERT INTO goods_need_register ( delta, order_item_id, goods_request_id, type, registrator_id, registered_at, register_operation_type_code, registrator_type_code, base_product_id, created_at, created_by ) 
            SELECT
                sgri.delta,
                sgri.order_item_id,
                sgri.goods_request_id,
                :supplier_reserve,
                sgr.id,
                sgr.created_at,
                :operation_supplier_goods_reservation,
                :supplier_goods_reservation,
                sgri.base_product_id,
                NOW(),
                :user_id::INTEGER 
            FROM
                supplier_goods_reservation AS sgr
                JOIN supplier_goods_reservation_item AS sgri ON sgr.id = sgri.supplier_goods_reservation_id 
            WHERE
                sgr.id IN (:supplier_goods_reservation_ids) 
                AND sri.quantity > 0
        ', new ResultSetMapping());
        $query->setParameter('supplier_reserve', GoodsNeedRegisterType::SUPPLIER_RESERVE);
        $query->setParameter('operation_supplier_goods_reservation', OperationTypeCode::SUPPLIER_GOODS_RESERVATION);
        $query->setParameter('supplier_goods_reservation', DocumentTypeCode::SUPPLIER_GOODS_RESERVATION);
        $query->setParameter('user_id', $currentUser->getId());
        $query->setParameter('supplier_goods_reservation_ids', $supplierGoodsReservationIds);

        $query->execute();
    }

    /**
     * Перепроводка резервирования с наличия
     *
     * @param array $availableGoodsReservationIds
     */
    public function availableGoodsReserve(array $availableGoodsReservationIds) : void
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $this->purge($availableGoodsReservationIds, DocumentTypeCode::AVAILABLE_GOODS_RESERVATION);

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $query = $em->createNativeQuery('
            INSERT INTO goods_reserve_register (
                base_product_id,
                supply_item_id,
                goods_condition_code,
                geo_room_id,
                order_item_id,
                goods_request_id,
                goods_issue_id,
                equipment_id,
                goods_release_id,
                supplier_invoice_item_id,
                delta,
                registrator_type_code,
                registrator_id,
                register_operation_type_code,
                registered_at,
                created_at,
                created_by) 
            SELECT 
                COALESCE ( oi.base_product_id, gr.base_product_id ) AS base_product_id,
                agri.supply_item_id,
                CASE WHEN agri.order_item_id > 0 
                        THEN :reserved 
                    WHEN gr.equipment_id > 0 
                        THEN :equipment 
                    ELSE :free 
                END AS goods_condition_code,
                agri.geo_room_id,
                agri.order_item_id,
                agri.goods_request_id,
                NULL AS goods_issue_id,
                gr.equipment_id,
                agri.goods_release_id,
                agri.supplier_invoice_item_id,
                agri.delta,
                :available_goods_reservation AS registrator_type_code,
                agr.id AS registrator_id,
                :operation_available_goods_reservation AS register_operation_type_code,
                agr.created_at AS registered_at,
                NOW() AS created_at,
                :user_id::INTEGER AS created_by 
            FROM
                available_goods_reservation AS agr
                INNER JOIN available_goods_reservation_item AS agri ON agri.available_goods_reservation_id = agr.id
                LEFT JOIN goods_request AS gr ON gr.id = agri.goods_request_id
                LEFT JOIN order_item AS oi ON oi.id = agri.order_item_id 
            WHERE
                agr.id IN (:available_goods_reservation_ids)
        ', new ResultSetMapping());
        $query->setParameter('reserved', GoodsConditionCode::RESERVED);
        $query->setParameter('equipment', GoodsConditionCode::EQUIPMENT);
        $query->setParameter('free', GoodsConditionCode::FREE);
        $query->setParameter('available_goods_reservation', DocumentTypeCode::AVAILABLE_GOODS_RESERVATION);
        $query->setParameter('operation_available_goods_reservation', OperationTypeCode::AVAILABLE_GOODS_RESERVATION);
        $query->setParameter('user_id', $currentUser->getId());
        $query->setParameter('available_goods_reservation_ids', $availableGoodsReservationIds);

        $query->execute();

        $query = $em->createNativeQuery('
            INSERT INTO goods_need_register (delta, order_item_id, goods_request_id, type, registrator_id, registered_at, register_operation_type_code, registrator_type_code, base_product_id, created_at, created_by )
            SELECT
                agri.delta,
                agri.order_item_id,
                agri.goods_request_id,
                :available_reserve AS type,
                :available_goods_reservation AS registrator_type_code,
                agr.id AS registrator_id,
                :operation_available_goods_reservation AS register_operation_type_code,
                agr.created_at AS registered_at,
                COALESCE ( oi.base_product_id, gr.base_product_id ) AS base_product_id,
                NOW() AS created_at,
                :user_id::INTEGER AS created_by 
            FROM
                available_goods_reservation AS agr
                INNER JOIN available_goods_reservation_item AS agri ON agri.available_goods_reservation_id = agr.id
                LEFT JOIN goods_request AS gr ON gr.id = agri.goods_request_id
                LEFT JOIN order_item AS oi ON oi.id = agri.order_item_id
            WHERE
                agr.id IN (:available_goods_reservation_ids)
        ', new ResultSetMapping());
        $query->setParameter('available_reserve', GoodsNeedRegisterType::AVAILABLE_RESERVE);
        $query->setParameter('available_goods_reservation', DocumentTypeCode::AVAILABLE_GOODS_RESERVATION);
        $query->setParameter('operation_available_goods_reservation', OperationTypeCode::AVAILABLE_GOODS_RESERVATION);
        $query->setParameter('user_id', $currentUser->getId());
        $query->setParameter('available_goods_reservation_ids', $availableGoodsReservationIds);

        $query->execute();
    }
}