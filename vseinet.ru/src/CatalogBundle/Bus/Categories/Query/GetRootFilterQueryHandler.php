<?php 

namespace CatalogBundle\Bus\Categories\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\CompetitorTypeCode;
use AppBundle\Enum\RepresentativeTypeCode;
use AppBundle\ORM\Query\DTORSM;

class GetRootFilterQueryHandler extends MessageHandler
{
    public function handle(GetRootFilterQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery('
            SELECT
                NEW CatalogBundle\Bus\Categories\Query\DTO\RootFilter (
                    C.id,
                    C.name
                )
            FROM
                PricingBundle:ProductToCompetitor AS pc
                JOIN ContentBundle:BaseProduct AS bp WITH bp.id = pc.baseProductId
                JOIN ContentBundle:CategoryPath AS cp WITH cp.id = bp.categoryId
                JOIN ContentBundle:Category AS C WITH C.id = cp.pid
            WHERE
                cp.plevel = 1
                AND pc.competitorId = :competitor_id
                AND ( pc.cityId IS NULL '.(!empty($query->cityId) ? 'OR pc.cityId = :city_id' : '').' )
            GROUP BY
                C.id
            ORDER BY
                C.name
        ');

        $q->setParameter('competitor_id', $query->competitorId);
        if (!empty($query->cityId))
            $q->setParameter('city_id', $query->cityId);

        return $q->getResult();
    }
}