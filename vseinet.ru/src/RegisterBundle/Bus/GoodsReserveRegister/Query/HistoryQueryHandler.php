<?php 

namespace RegisterBundle\Bus\GoodsReserveRegister\Query;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;

class HistoryQueryHandler extends MessageHandler
{
    public function handle(HistoryQuery $query)
    {   
        $whereQuery = []; 

        if ($query->baseProductId) {
            $whereQuery[] = "grr.baseProductId = :baseProductId";
        }

        if ($query->roomId) {
            $whereQuery[] = "grr.geoRoomId = :roomId";
        }

        if ($query->orderNumber) {
            $whereQuery[] = "oi.orderId = :orderNumber";
        }

        if ($query->registeredFrom) {
            $whereQuery[] = "grr.registeredAt >= :registeredFrom";
        }

        if ($query->registeredTo) {
            $whereQuery[] = "grr.registeredAt <= :registeredTo";
        }

        if ($query->productCondition) {
            $whereQuery[] = "grr.goodsConditionCode <= :productCondition";
        }

        if ($query->parentDocNumber) {
            $whereQuery[] = "grr.registratorId <= :parentDocNumber";
        }

        if ($query->parentDocType) {
            $whereQuery[] = "grr.registratorTypeCode <= :parentDocType";
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                COUNT(grr.id) AS total, COALESCE(SUM(grr.delta), 0) AS quantity
            FROM RegisterBundle:GoodsReserveRegister AS grr
            LEFT JOIN OrderBundle:OrderItem AS oi WITH oi.id = grr.orderItemId
            " . ($whereQuery ? "WHERE " . implode(" AND ", $whereQuery) : "")
        );

        if ($query->baseProductId) {
            $q->setParameter('baseProductId', $query->baseProductId);
        }
        if ($query->roomId) {
            $q->setParameter('roomId', $query->roomId);
        }
        if ($query->orderNumber) {
            $q->setParameter('orderNumber', $query->orderNumber);
        }
        if ($query->registeredFrom) {
            $q->setParameter('registeredFrom', $query->registeredFrom);
        }
        if ($query->registeredTo) {
            $q->setParameter('registeredTo', $query->registeredTo);
        }
        if ($query->productCondition) {
            $q->setParameter('productCondition', $query->productCondition);
        }
        if ($query->parentDocNumber) {
            $q->setParameter('parentDocNumber', $query->parentDocNumber);
        }
        if ($query->parentDocType) {
            $q->setParameter('parentDocType', $query->parentDocType);
        }
        $aggResults = $q->getScalarResult();
        
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT                
                grr.id,
                grr.registeredAt,
                grr.baseProductId,
                bp.name as productName,
                grr.goodsConditionCode,
                grr.geoRoomId,
                CONCAT(gc.name, ', ', gp.code, ', ', gr.name) AS geoRoomName,
                grr.supplyItemId,
                CASE 
                    WHEN si.parentDocType = 'supply' 
                        THEN CONCAT('Накладная поставщика №', si.parentDocId) 
                    WHEN si.parentDocType = 'goods_issue' 
                        THEN CONCAT('Товарная претензия №', si.parentDocId) 
                    WHEN si.parentDocType = 'inventory' 
                        THEN CONCAT('Ивентаризация №', si.parentDocId) 
                    WHEN si.parentDocType = 'goods_packaging' 
                        THEN CONCAT('Разбивка товара №', si.parentDocId)
                    ELSE '' 
                END as supplyItemParentDocTitle,
                grr.orderItemId,
                CONCAT('Заказ №', o.id, ' от ', DATE_FORMAT(o.createdAt, '%d.%m.%Y')) as orderTitle,
                grr.delta,
                CASE
                    WHEN grr.registratorTypeCode = 'order' 
                        THEN CONCAT('Заказ №', si.parentDocId) 
                    WHEN grr.registratorTypeCode = 'goods_issue' 
                        THEN CONCAT('Товарная претензия №', si.parentDocId) 
                    WHEN grr.registratorTypeCode = 'goods_acceptance' 
                        THEN CONCAT('Приемка товара №', si.parentDocId) 
                    WHEN grr.registratorTypeCode = 'goods_release' 
                        THEN CONCAT('Отгрузка товара №', si.parentDocId) 
                    WHEN grr.registratorTypeCode = 'inventory' 
                        THEN CONCAT('Ивентаризация №', si.parentDocId) 
                    WHEN grr.registratorTypeCode = 'goods_movement' 
                        THEN CONCAT('Перемещение товара №', si.parentDocId) 
                    WHEN grr.registratorTypeCode = 'goods_packaging' 
                        THEN CONCAT('Разбивка товара №', si.parentDocId) 
                    WHEN grr.registratorTypeCode = 'supplier_reserve' 
                        THEN CONCAT('Резерв поставщика №', si.parentDocId) 
                    WHEN grr.registratorTypeCode = 'available_goods_reservation' 
                        THEN CONCAT('Резервирование товара №', si.parentDocId) 
                    WHEN grr.registratorTypeCode = 'order_annul' 
                        THEN CONCAT('Аннулирование заказа №', si.parentDocId) 
                    WHEN grr.registratorTypeCode = 'goods_issue_decision' 
                        THEN CONCAT('Решение по товарной претензии №', si.parentDocId)
                    WHEN grr.registratorTypeCode = 'supply' 
                        THEN CONCAT('Накладная поставщика №', si.parentDocId) 
                    WHEN grr.registratorTypeCode = 'order_receipt' 
                        THEN CONCAT('Товарный чек', si.parentDocId)
                    ELSE '' 
                END as parentDocTitle
            FROM RegisterBundle:GoodsReserveRegister AS grr
            LEFT JOIN ContentBundle:BaseProduct AS bp WITH bp.id = grr.baseProductId
            LEFT JOIN OrderBundle:OrderItem AS oi WITH oi.id = grr.orderItemId
            LEFT JOIN OrderBundle:OrderTable AS o WITH o.id = oi.orderId
            LEFT JOIN SupplyBundle:SupplyItem AS si WITH si.id = grr.supplyItemId
            LEFT JOIN GeoBundle:GeoRoom AS gr WITH gr.id = grr.geoRoomId
            LEFT JOIN GeoBundle:GeoPoint AS gp WITH gp.id = gr.geoPointId
            LEFT JOIN ContentBundle:GeoCity AS gc WITH gc.id = gp.geoCityId
            " . ($whereQuery ? "WHERE " . implode(" AND ", $whereQuery) : "") ."
            ORDER BY 
                grr.registeredAt,
                grr.registratorTypeCode,
                grr.delta
        ");
        if ($query->baseProductId) {
            $q->setParameter('baseProductId', $query->baseProductId);
        }
        if ($query->roomId) {
            $q->setParameter('roomId', $query->roomId);
        }
        if ($query->orderNumber) {
            $q->setParameter('orderNumber', $query->orderNumber);
        }
        if ($query->registeredFrom) {
            $q->setParameter('registeredFrom', $query->registeredFrom);
        }
        if ($query->registeredTo) {
            $q->setParameter('registeredTo', $query->registeredTo);
        }
        if ($query->productCondition) {
            $q->setParameter('productCondition', $query->productCondition);
        }
        if ($query->parentDocNumber) {
            $q->setParameter('parentDocNumber', $query->parentDocNumber);
        }
        if ($query->parentDocType) {
            $q->setParameter('parentDocType', $query->parentDocType);
        }
        $q->setMaxResults($query->limit);
        $q->setFirstResult(($query->page - 1) * $query->limit);
        $items = $q->getArrayResult();

        return new DTO\Items($items, $aggResults[0]['total'], $aggResults[0]['quantity']);
    }

}