<?php 

namespace GeoBundle\Service;

use AppBundle\Container\ContainerAware;

class RepresentativeIdentity extends ContainerAware
{
    public function getCentral(int $geoCityId): DTO\Representative
    {
        $session = $this->get('request_stack')->getMasterRequest()->getSession();

        $representative = $session->get('representative');
        if (null === $representative || $representative->geoCityId != $geoCityId) {
            $q = $this->getDoctrine()->getManager()->createQuery("
                SELECT
                    NEW GeoBundle\Service\DTO\Representative (
                        gp.geoCityId,
                        r.geoPointId
                    ),
                    CASE WHEN gc.id = :geoCityId THEN 1 ELSE 2 END AS HIDDEN ORD 
                FROM OrgBundle:Representative AS r 
                INNER JOIN GeoBundle:GeoPoint AS gp WITH gp.id = r.geoPointId 
                INNER JOIN GeoBundle:GeoCity AS gc WITH gc.id = gp.geoCityId
                WHERE r.isActive = true AND r.isCentral = true AND gc.id IN (:geoCityId, :defaultGeoCityId) 
                ORDER BY ORD 
            ");
            $q->setParameter('geoCityId', $geoCityId ?: $this->getParameter('default.city.id'));
            $q->setParameter('defaultGeoCityId', $this->getParameter('default.city.id'));
            $q->setMaxResults(1);
            $representative = $q->getSingleResult();

            $session->set('representative', $representative);
        }

        return $representative;
    }

    public function invalidate(): void
    {
        $this->get('request_stack')->getMasterRequest()->getSession()->remove('representative');
    } 
}
