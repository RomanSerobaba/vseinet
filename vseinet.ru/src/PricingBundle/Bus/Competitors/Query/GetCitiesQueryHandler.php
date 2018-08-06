<?php

namespace PricingBundle\Bus\Competitors\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\CompetitorTypeCode;
use AppBundle\Enum\RepresentativeTypeCode;
use AppBundle\ORM\Query\DTORSM;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use PricingBundle\Component\CompetitorsComponent;
use PricingBundle\Entity\Product;

class GetCitiesQueryHandler extends MessageHandler
{
    public function handle(GetCitiesQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createNativeQuery('
            WITH DATA AS (
            SELECT
                0 :: INTEGER AS id,
                \'Вся Россия\' AS name
                UNION ALL
                (
            SELECT
                c.ID,
                c.NAME
            FROM
                representative AS r
                JOIN geo_point AS p ON p.ID = r.geo_point_id
                JOIN geo_city AS c ON c.ID = p.geo_city_id
            WHERE
                r.is_active = TRUE
                AND r.has_retail = TRUE
                AND r."type" IN ( :our, :partner )
            ORDER BY
                c.is_central DESC,
                c.NAME
                )
                )
                SELECT
                d.id,
                d.name,
                COUNT ( c.ID ) AS loose_count
            FROM
                DATA AS d
                LEFT JOIN product_to_competitor AS pc ON ( d.ID = pc.geo_city_id OR d.ID = 0 AND pc.geo_city_id IS NULL )
                LEFT JOIN product AS pr ON pr.base_product_id = pc.base_product_id
                AND ( pr.geo_city_id = pc.geo_city_id OR pc.geo_city_id IS NULL AND pr.geo_city_id IS NULL )
                LEFT JOIN competitor AS c ON c.ID = pc.competitor_id
                AND c.is_active = TRUE
                AND ( c.channel = :site AND pc.server_response = 200 OR c.channel != :site AND pc.competitor_price > 0 )
                AND pc.price_time + CASE WHEN c.channel = :retail THEN INTERVAL \'1 month\' ELSE INTERVAL \'3 day\' END > NOW( )
                AND pr.price > pc.competitor_price
            GROUP BY
                d.id,
                d.name
            ORDER BY CASE WHEN d.ID = 0 THEN 0 ELSE 1 END
        ', new DTORSM(\PricingBundle\Bus\Competitors\Query\DTO\Cities::class));

        $q->setParameter('our', RepresentativeTypeCode::OUR);
        $q->setParameter('partner', RepresentativeTypeCode::PARTNER);
        $q->setParameter('site', CompetitorTypeCode::SITE);
        $q->setParameter('retail', CompetitorTypeCode::RETAIL);

        return $q->getResult('DTOHydrator');
    }
}