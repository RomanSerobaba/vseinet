<?php 

namespace OrgBundle\Bus\Representative\Query;

use AppBundle\Enum\RepresentativeTypeCode;
use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;

class GetRepresentativePointsQueryHandler extends MessageHandler
{
    public function handle(GetRepresentativePointsQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $sql = '
            SELECT
                r.geo_point_id AS id,
                gp.code,
                gp.name 
            FROM
                representative AS r
                JOIN geo_point AS gp ON gp.id = r.geo_point_id 
            '.($query->isRetailOnly ? '
            WHERE
                r."type" IN ( :our, :partner ) 
                AND r.is_active = TRUE 
                AND r.has_retail = TRUE
            ' : '').' 
            ORDER BY
                gp.code
        ';

        $q = $em->createNativeQuery($sql, new DTORSM(\OrgBundle\Bus\Representative\Query\DTO\RepresentativePoints::class));
        $q->setParameter('our', RepresentativeTypeCode::OUR);
        $q->setParameter('partner', RepresentativeTypeCode::PARTNER);

        return $q->getResult('DTOHydrator');
    }
}