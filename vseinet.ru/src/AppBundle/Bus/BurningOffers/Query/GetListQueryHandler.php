<?php

namespace AppBundle\Bus\BurningOffers\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Doctrine\ORM\Query\DTORSM;
use AppBundle\Enum\ProductAvailabilityCode;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        $geoCity = $this->getGeoCity();

        return $this->getDoctrine()->getManager()->createNativeQuery("
            SELECT
                bp.id,
                bp.name,
                bpi.basename AS base_src,
                COALESCE ( p2.price, p.price ) AS price,
                b.sef_url,
                coalesce((SELECT 1
                    from goods_reserve_register_current as grrc
                    inner join base_product as bp2 on bp2.id = grrc.base_product_id
                    inner join geo_room as gr on gr.id = grrc.geo_room_id
                    inner join representative as r on r.geo_point_id = gr.geo_point_id
                where bp2.canonical_id = bp.id and grrc.order_item_id is null and grrc.goods_condition_code = 'free' and (r.type != 'franchiser' and mr.type != 'franchiser' or r.type = 'franchiser' and mr.type = 'franchiser' and r.company_id = mr.company_id)
                limit 1), 0) as is_available,
                bpd.short_description
            FROM
                base_product AS b
                INNER JOIN product_total_sale AS pts ON ( pts.base_product_id = b.ID )
                inner join base_product as bp on bp.id = b.canonical_id
                LEFT JOIN product AS p2 ON ( p2.base_product_id = b.ID AND p2.geo_city_id = :geoCityId )
                INNER JOIN product AS p ON ( p.base_product_id = b.ID AND p.geo_city_id = 0 )
                LEFT JOIN base_product_image AS bpi ON bpi.base_product_id = b.ID AND bpi.sort_order = 1 AND bpi.width > 0
                left join base_product_data as bpd on bpd.base_product_id = bp.id
                left join representative as mr on mr.geo_city_id = :geoCityId and mr.is_active = true and mr.is_central = true
            WHERE
                b.id = b.canonical_id AND p.product_availability_code > :outOfStock
        ", new DTORSM(DTO\Product::class))
            ->setParameter('geoCityId', $geoCity->getId())
            ->setParameter('outOfStock', ProductAvailabilityCode::OUT_OF_STOCK)
            ->getResult('DTOHydrator');
    }
}
