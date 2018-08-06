<?php

namespace OrgBundle\Bus\Representative\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\RepresentativeTypeCode;

class GetPointsQueryHandler extends MessageHandler
{
    /**
     * @param GetPointsQuery $query
     *
     * @return array
     */
    public function handle(GetPointsQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $sql = '
            SELECT
                gp.id,
                concat (gp.geoCity, \' / \', gp.code ) AS name 
            FROM
                SupplyBundle:ViewGeoPoint AS gp
                JOIN OrgBundle:Representative AS r WITH r.geoPointId = gp.id 
            WHERE
                r.type IN (:our, :partner) 
                AND r.isActive = TRUE 
                AND r.hasRetail = TRUE 
            ORDER BY
                gp.geoCity,
                gp.name
        ';

        $q = $em->createQuery($sql);
        $q->setParameter('our', RepresentativeTypeCode::OUR);
        $q->setParameter('partner', RepresentativeTypeCode::PARTNER);

        return $q->getArrayResult();
    }
}