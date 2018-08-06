<?php

namespace OrgBundle\Bus\Representative\Query;

use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Enum\RepresentativeTypeCode;
use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;

class GetRepresentativeReservesQueryHandler extends MessageHandler
{
    public function handle(GetRepresentativeReservesQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $sql = '
            SELECT
                r.geo_point_id AS representative_id,
                SUM( grr.delta * si.purchase_price )::BIGINT AS reserve_amount
            FROM
                get_goods_reserve_register_data ( CURRENT_TIMESTAMP :: TIMESTAMP ) AS grr
                JOIN geo_room AS r ON r.id = grr.geo_room_id
                JOIN supply_item AS si ON si.id = grr.supply_item_id
            WHERE
                grr.goods_condition_code = :free
            GROUP BY
                r.geo_point_id
        ';
        $q = $em->createNativeQuery($sql, new DTORSM(\OrgBundle\Bus\Representative\Query\DTO\RepresentativeReserves::class));
        $q->setParameter('free', GoodsConditionCode::FREE);

        return $q->getResult('DTOHydrator');
    }
}