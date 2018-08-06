<?php

namespace SupplyBundle\Bus\Orders\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\GoodsReserveOperationCode;
use AppBundle\Enum\GoodsReserveType;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CancelPositionCommandHandler extends MessageHandler
{
    public function handle(CancelPositionCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('
            SELECT
                base_product_id 
            FROM
                order_item 
            WHERE
                is_annulled != TRUE 
                AND is_completed != TRUE 
                AND id = : order_item_id
        ');
        $query->setParameter('order_item_id', $command->orderItemId);

        $items = $query->getArrayResult();

        if (empty($items)) {
            throw new BadRequestHttpException('Позиция находится в недопустимом статусе');
        }

        $query = $em->createQuery('
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
        ');
        $query->setParameter('order_item_id', $command->orderItemId);

        $logs = $query->getArrayResult();

        $reserveService = $this->get('service.reserve');

        foreach ($logs as $log) {
            /**
             * Вызываем методы изменения логов резервов
             */
            $reserveService->change(
                $command->baseProductId,
                $log['supply_item_id'],
                DocumentTypeCode::ORDER_ITEM,
                $command->orderItemId,
                GoodsReserveOperationCode::ANNUL_RESERVE,
                GoodsReserveType::NEW,
                $log['geo_room_id'],
                -$log['amount'],
                null,
                $command->orderItemId,
                null,
                null,
                null,
                $log['shipment_id']
            );

            $reserveService->change(
                $command->baseProductId,
                $log['supply_item_id'],
                DocumentTypeCode::ORDER_ITEM,
                $command->orderItemId,
                GoodsReserveOperationCode::ANNUL_RESERVE,
                GoodsReserveType::NEW,
                $log['geo_room_id'],
                $log['amount'],
                null,
                null,
                null,
                null,
                null,
                $log['shipment_id']
            );
        }

        /**
         * Установка временной цены
         */
        $query = $em->createQuery('
            UPDATE order_item 
            SET is_annulled = TRUE 
            WHERE
                id = :order_item_id
        ');
        $query->setParameter('order_item_id', $command->orderItemId);

        $query->execute();
    }
}
