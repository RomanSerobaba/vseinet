<?php 

namespace PricingBundle\Bus\Competitors\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\CompetitorTypeCode;
use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\Enum\ProductToCompetitorStatus;
use AppBundle\Enum\RepresentativeTypeCode;
use AppBundle\ORM\Query\DTORSM;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use PricingBundle\Component\CompetitorsComponent;
use PricingBundle\Entity\Product;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createNativeQuery('
            SELECT
                C.id,
                C.name,
                C.link,
                C.is_active,
                C.supplier_id,
                C.channel AS type_code,
                s.code AS supplier,
                C.geo_city_id,
                gc.name AS city,
                ga.address,
                ga.geo_street_id,
                ga.house,
                ga.building,
                ga.floor,
                COUNT( P.base_product_id ) AS checking_count,
                COUNT(
                  CASE WHEN pc.server_response != 200 
                      AND C.channel = :site 
                      OR COALESCE ( pc.competitor_price, 0 ) = 0 
                    THEN P.base_product_id 
                    ELSE NULL 
                END 
                ) AS failed_count,
                COUNT(
                  CASE WHEN ( pc.server_response = 200 OR C.channel != :site AND pc.competitor_price > 0 ) 
                      THEN P.base_product_id 
                      ELSE NULL 
                  END 
                ) AS successful_count,
                COUNT(
                    CASE WHEN ( pc.server_response = 200 OR C.channel != :site AND pc.competitor_price > 0 ) 
                            AND P.price <= pc.competitor_price 
                        THEN P.base_product_id 
                        ELSE NULL 
                    END 
                ) AS competitive_count,
                COUNT(
                    CASE WHEN ( pc.server_response = 200 OR C.channel != :site AND pc.competitor_price > 0 ) 
                        AND P.price > pc.competitor_price 
                        THEN P.base_product_id 
                        ELSE NULL 
                    END 
                ) AS loosing_count 
            FROM 
                competitor AS C
                LEFT JOIN geo_city AS gc ON gc.id = C.geo_city_id
                LEFT JOIN geo_address AS ga ON ga.id = C.geo_address_id
                LEFT JOIN supplier AS s ON s.id = C.supplier_id
                LEFT JOIN product_to_competitor AS pc ON pc.competitor_id = C.id 
                    AND ( pc.geo_city_id IS NULL '.($query->cityId > 0 ? 'OR pc.geo_city_id = :city_id' : '') . ' ) 
                    AND pc.status = :completed 
                    AND pc.price_time + CASE WHEN C.channel = :retail 
                        THEN INTERVAL \'1 month\' 
                        ELSE INTERVAL \'3 day\' 
                        END > now( )
                    LEFT JOIN product AS P ON P.base_product_id = pc.base_product_id 
                    AND '.(!empty($query->cityId) ? 'P.geo_city_id = :city_id' : 'P.geo_city_id IS NULL').' 
                    AND P.product_availability_code = :available 
            WHERE
                '.(!empty($query->cityId) ? 'COALESCE ( C.geo_city_id, :city_id ) = :city_id' : 'C.geo_city_id IS NULL').'
            GROUP BY
                C.id,
                s.id,
                gc.id,
                ga.id
        ', new DTORSM(\PricingBundle\Bus\Competitors\Query\DTO\GetList::class));

        $q->setParameter('site', CompetitorTypeCode::SITE);
        $q->setParameter('retail', CompetitorTypeCode::RETAIL);
        $q->setParameter('completed', ProductToCompetitorStatus::COMPLETED);
        $q->setParameter('available', ProductAvailabilityCode::AVAILABLE);
        $q->setParameter('city_id', $query->cityId);

        return $q->getResult('DTOHydrator');
    }
}