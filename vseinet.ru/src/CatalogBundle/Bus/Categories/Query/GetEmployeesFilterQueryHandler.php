<?php 

namespace CatalogBundle\Bus\Categories\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\CompetitorTypeCode;
use AppBundle\Enum\RepresentativeTypeCode;
use AppBundle\ORM\Query\DTORSM;

class GetEmployeesFilterQueryHandler extends MessageHandler
{
    public function handle(GetEmployeesFilterQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery('
            SELECT
                NEW CatalogBundle\Bus\Categories\Query\DTO\EmployeesFilter (
                    vup.userId,
                    vup.fullname
                )
            FROM
                PricingBundle:ProductToCompetitor AS pc
                JOIN OrgBundle:Employee AS e WITH e.userId = pc.createdBy
                JOIN AppBundle:ViewUserPerson AS vup WITH vup.userId = e.userId 
            WHERE
                pc.competitorId = :competitor_id
                AND ( pc.cityId IS NULL '.(!empty($query->cityId) ? 'OR pc.cityId = :city_id' : '').' ) 
            GROUP BY
                vup.userId,
                vup.fullname 
            ORDER BY
                vup.fullname
        ');

        $q->setParameter('competitor_id', $query->competitorId);
        if (!empty($query->cityId))
            $q->setParameter('city_id', $query->cityId);

        return $q->getResult();
    }
}