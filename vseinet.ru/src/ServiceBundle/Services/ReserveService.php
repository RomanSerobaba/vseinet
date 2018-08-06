<?php

namespace ServiceBundle\Services;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\GoodsConditionCode;
use Doctrine\ORM\Query\ResultSetMapping;
use AppBundle\ORM\Query\DTORSM;

class ReserveService extends MessageHandler
{
    /**
     * Изменить резерв
     *
     * @param int    $baseProductId
     * @param int    $supplyItemId
     * @param string $documentType
     * @param int    $documentId
     * @param string $goodsReserveOperationCode
     * @param string $reserveType
     * @param int    $geoRoomId
     * @param int    $delta
     * @param        $operatedAt
     * @param        $orderItemId
     * @param        $goodsRequestId
     * @param        $goodsIssueId
     * @param        $equipmentId
     * @param        $shipmentId
     *
     * @return mixed
     */
    public function change(
        int $baseProductId,
        int $supplyItemId,
        string $documentType,
        int $documentId,
        string $goodsReserveOperationCode,
        string $reserveType,
        int $geoRoomId,
        int $delta,
        $operatedAt,
        $orderItemId,
        $goodsRequestId,
        $goodsIssueId,
        $equipmentId,
        $shipmentId
    ): mixed {
        if (empty($operatedAt)) {
            $operatedAt = 'NOW()';
        }

        $currentUserId = null;

        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var \AppBundle\Entity\User $user
         */
        $user = $this->get('user.identity')->getUser();
        if (!empty($user)) {
            $currentUserId = $user->getId();
        }

        $query = $em->createNativeQuery('
            INSERT INTO goods_reserve_log (
                operated_at,
                base_product_id,
                supply_item_id,
                document_type,
                document_id,
                goods_reserve_operation_code,
                reserve_type,
                geo_room_id,
                order_item_id,
                goods_request_id,
                goods_issue_id,
                equipment_id,
                shipment_id,
                delta,
                created_at,
                created_by 
            )
            VALUES
            (
                :operated_at,
                :base_product_id,
                :supply_item_id,
                :document_type,
                :document_id,
                :goods_reserve_operation_code,
                :reserve_type,
                :geo_room_id,
                :order_item_id,
                :goods_request_id,
                :goods_issue_id,
                :equipment_id,
                :shipment_id,
                :delta,
                NOW( ),
                :current_user_id 
            )
        ', new ResultSetMapping());
        $query->setParameter('operated_at', $operatedAt);
        $query->setParameter('base_product_id', $baseProductId);
        $query->setParameter('supply_item_id', $supplyItemId);
        $query->setParameter('document_type', $documentType);
        $query->setParameter('document_id', $documentId);
        $query->setParameter('goods_reserve_operation_code', $goodsReserveOperationCode);
        $query->setParameter('reserve_type', $reserveType);
        $query->setParameter('geo_room_id', $geoRoomId);
        $query->setParameter('order_item_id', $orderItemId);
        $query->setParameter('goods_request_id', $goodsRequestId);
        $query->setParameter('goods_issue_id', $goodsIssueId);
        $query->setParameter('equipment_id', $equipmentId);
        $query->setParameter('shipment_id', $shipmentId);
        $query->setParameter('delta', $delta);
        $query->setParameter('current_user_id', $currentUserId);

        return $query->execute();
    }

    /**
     * Получить список свободных остатков товара по точкам
     *
     * @param int $baseProductId
     *
     * @return array
     */
    public function getFree(int $baseProductId) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createNativeQuery('
            SELECT
                vgp.id AS point_id,
                SUM( r.quantity )::INTEGER AS quantity,
                SUM( CASE WHEN r.geo_room_id > 0 AND gr.type = :shop THEN r.quantity ELSE 0 END )::INTEGER AS shop_quantity,
                vgp.geo_city AS city,
                CONCAT ( vgp.code, CASE WHEN r.geo_room_id IS NULL THEN \', транзит\' ELSE \'\' END ) AS point_code,
                CASE WHEN r.geo_room_id IS NULL THEN TRUE ELSE FALSE END AS is_in_transit             
            FROM (
                SELECT
                    grr.geo_room_id,
                    grr.goods_release_id,
                    CASE WHEN grr.geo_room_id IS NULL AND grr.goods_release_id IS NULL 
                        THEN si.parent_doc_id 
                        ELSE NULL 
                    END AS supply_id,
                    SUM( grr.delta ) AS quantity 
                FROM
                    goods_reserve_register_current AS grr
                    LEFT JOIN supply_item AS si ON si.id = grr.supply_item_id 
                    WHERE grr.base_product_id = :base_product_id AND grr.goods_condition_code = :free                   
                GROUP BY
                    grr.geo_room_id,
                    grr.goods_release_id,
                    CASE WHEN grr.geo_room_id IS NULL AND grr.goods_release_id IS NULL 
                        THEN si.parent_doc_id 
                        ELSE NULL 
                    END 
                HAVING
                    SUM( grr.delta ) > 0 
                ) AS r
                LEFT JOIN goods_release_doc AS gre ON gre.number = r.goods_release_id
                LEFT JOIN geo_room AS gr ON gr.id = COALESCE ( r.geo_room_id, gre.destination_room_id )
                LEFT JOIN supply AS s ON s.id = r.supply_id
                JOIN view_geo_point AS vgp ON vgp.id = COALESCE ( gr.geo_point_id, s.destination_point_id ) 
            GROUP BY
                vgp.id,
                is_in_transit,
                vgp.geo_city,
                vgp.code,
                vgp.is_central_city,
                r.geo_room_id 
            ORDER BY
                vgp.is_central_city DESC,
                vgp.geo_city,
                vgp.code,
                is_in_transit
        ', new DTORSM(\OrderBundle\Bus\Reserves\Query\DTO\ReservePointsQuery::class));
        $query->setParameter('base_product_id', $baseProductId);
        $query->setParameter('free', GoodsConditionCode::FREE);
        $query->setParameter('shop', 'shop');

        return $query->getResult('DTOHydrator');
    }
}