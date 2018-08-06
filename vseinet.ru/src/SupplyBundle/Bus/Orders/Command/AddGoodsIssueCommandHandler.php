<?php 

namespace SupplyBundle\Bus\Orders\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\GoodsReserveType;

class AddGoodsIssueCommandHandler extends MessageHandler
{
    public function handle(AddGoodsIssueCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('
            INSERT INTO goods_issue ( base_product_id, quantity, goods_issue_type_code, order_item_id, created_at, created_by, description, geo_room_id, product_resort_id ) 
            SELECT
                base_product_id,
                SUM( delta ),
                :goods_issue_type_code,
                :order_item_id,
                NOW( ),
                :user_id::INTEGER,
                :description,
                MIN( geo_room_id ),
                :product_resort_id 
            FROM (
                SELECT
                    base_product_id,
                    delta,
                    geo_room_id,
                    RANK ( ) OVER ( PARTITION BY order_item_id, reserve_type, geo_room_id ORDER BY operated_at DESC ) 
                FROM
                    goods_reserve_log 
                WHERE
                    reserve_type = :new 
                    AND order_item_id = :order_item_id 
            ) AS reserves 
            WHERE
                RANK = 1 
            GROUP BY
                base_product_id
        ');
        $query->setParameter('goods_issue_type_code', $command->code);
        $query->setParameter('order_item_id', $command->orderItemId);
        $query->setParameter('text', $command->description);
        $query->setParameter('user_id', $command->userId);
        $query->setParameter('product_resort_id', $command->productResortId);
        $query->setParameter('new', GoodsReserveType::NEW);

        $query->execute();
    }
}