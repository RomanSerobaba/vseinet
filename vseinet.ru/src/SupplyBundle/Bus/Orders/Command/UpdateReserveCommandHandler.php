<?php 

namespace SupplyBundle\Bus\Orders\Command;

use AppBundle\Bus\Message\MessageHandler;
use ServiceBundle\Services\ReserveService;

class UpdateReserveCommandHandler extends MessageHandler
{
    public function handle(UpdateReserveCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var ReserveService $reserveService
         */

        /*
        $reserveService = $this->get('services.reserve');

        foreach ($command->warehouse as $geoPointID => $quantity) {
            if ($quantity > 0) {
                $query = $em->createQuery('
                    SELECT
                        goods_reserve_log.supply_item_id,
                        goods_reserve_log.geo_room_id,
                        SUM(goods_reserve_log.delta) AS amount 
                    FROM
                        goods_reserve_log
                        JOIN geo_room ON geo_room.id = goods_reserve_log.geo_room_id 
                    WHERE
                        order_item_id IS NULL 
                        AND goods_reserve_log.reserve_type = :new 
                        AND base_product_id = :base_product_id 
                        AND geo_room.geo_point_id = :geo_point_id 
                    GROUP BY
                        goods_reserve_log.supply_item_id,
                        goods_reserve_log.geo_room_id 
                    HAVING
                        SUM(delta) > 0
                ');
                $query->setParameter('new', GoodsReserveLog::RESERVE_TYPE_NEW);
                $query->setParameter('base_product_id', $command->baseProductId);
                $query->setParameter('geo_point_id', $geoPointID);

                $rows = $query->getArrayResult();

                foreach ($rows as $row) {
                    if ($quantity <= 0) {
                        break;
                    }

                    if ($quantity >= $row['quantity']) {
                        $delta = $row['quantity'];
                        $quantity -= $delta;
                    } else {
                        $delta = $quantity;
                        $quantity = 0;
                    }



                    $reserveService->change(
                        $command->baseProductId,
                        $row['supply_item_id'],
                        GoodsReserveLog::DOCUMENT_TYPE_ORDER_ITEM,
                        document_id = :order_item_id,
                        GoodsReserveLog::GOODS_RESERVE_OPERATION_CODE_RESERVE,
                        GoodsReserveLog::RESERVE_TYPE_NEW,
                        $row['geo_room_id'],
                        -$delta
                    );

                    $reserveService->change(
                        $command->baseProductId,
                        $row['supply_item_id'],
                        GoodsReserveLog::DOCUMENT_TYPE_ORDER_ITEM,
                        document_id = :order_item_id,
                        GoodsReserveLog::GOODS_RESERVE_OPERATION_CODE_RESERVE,
                        GoodsReserveLog::RESERVE_TYPE_NEW,
                        $row['geo_room_id'],
                        $delta
                    );
                }
            }
        }

        foreach ($command->transit as $geoPointID => $quantity) {
            if ($quantity > 0) {
                $query = $em->createQuery('
                    SELECT
                        goods_reserve_log.goods_request_id,
                        goods_reserve_log.shipment_id,
                        goods_reserve_log.supply_item_id,
                        goods_reserve_log.geo_room_id,
                        SUM(goods_reserve_log.delta) AS amount 
                    FROM
                        goods_reserve_log
                        JOIN geo_room ON geo_room.id = goods_reserve_log.geo_room_id 
                    WHERE
                        order_item_id IS NULL 
                        AND base_product_id = :base_product_id 
                        AND goods_reserve_log.reserve_type = :new 
                        AND geo_room.geo_point_id = :geo_point_id 
                    GROUP BY
                        goods_reserve_log.supply_item_id,
                        goods_reserve_log.geo_room_id,
                        goods_reserve_log.goods_request_id,
                        goods_reserve_log.shipment_id 
                    HAVING
                        SUM(delta) > 0
                ');
                $query->setParameter('new', GoodsReserveLog::RESERVE_TYPE_NEW);
                $query->setParameter('base_product_id', $command->baseProductId);
                $query->setParameter('geo_point_id', $geoPointID);

                $rows = $query->getArrayResult();
            }
        }

*/
    }
}