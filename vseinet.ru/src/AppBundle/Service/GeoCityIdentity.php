<?php

namespace AppBundle\Service;

use AppBundle\Container\ContainerAware;
use AppBundle\Entity\GeoCity;

class GeoCityIdentity extends ContainerAware
{
    public function getGeoCity(): GeoCity
    {
        $request = $this->get('request_stack')->getMasterRequest();
        $session = $request->getSession();

        $geoCity = $session->get('geo_city');
        if (null === $geoCity) {
            $geoCity = $this->loadGeoCity($this->detectGeoCityId());
            $session->set('geo_city', $geoCity);
        }

        return $geoCity;
    }

    /**
     * @param int $geoCityId
     */
    public function setGeoCityId(int $geoCityId): void
    {
        $this->get('request_stack')->getMasterRequest()->getSession()->set('geo_city', $this->loadGeoCity($geoCityId));
    }

    protected function detectGeoCityId(): int
    {
        $em = $this->getDoctrine()->getManager();

        $token = $this->get('security.token_storage')->getToken();
        if (null !== $token) {
            $user = $token->getUser();
            if (is_object($user)) {
                $geoCityId = $user->getGeoCityId();
                if ($geoCityId) {
                    return $geoCityId;
                }
                $q = $em->createQuery("
                    SELECT ga.geoCityId, CASE WHEN ga.isMain IS TRUE THEN 1 ELSE 2 END AS HIDDEN ORD
                    FROM AppBundle:GeoAddress AS ga
                    INNER JOIN AppBundle::GeoAddressToPerson AS ga2p WITH ga2p.geoAddressId = ga.id
                    WHERE ga2p.personId = :personId AND ga.geoCityId IS NOT NULL
                    ORDER BY ORD
                ");
                $q->setParameter('personId', $user->getPersonId());
                $q->setMaxResults(1);
                $geoCityId = $q->getOneOrNullResult();
                if ($geoCityId) {
                    return $geoCityId;
                }
            }
        }
        $q = $em->createQuery("
            SELECT
                gi.geoCityId
            FROM AppBundle:GeoIp AS gi
            WHERE :longIp BETWEEN gi.longIp1 AND gi.longIp2
        ");
        $q->setParameter('longIp', ip2long($this->get('request_stack')->getMasterRequest()->getClientIp()));
        $q->setMaxResults(1);
        try {
            return $q->getSingleScalarResult();
        } catch (\Exception $e) {
        }

        return $this->getParameter('default.geo_city_id');
    }

    protected function loadGeoCity(int $geoCityId): GeoCity
    {
        $em = $this->getDoctrine()->getManager();

        $geoCity = $em->getRepository(GeoCity::class)->find($geoCityId);
        if (!$geoCity instanceof GeoCity) {
            throw new \RuntimeException('GeoCity not found');
        }

        $q = $em->createQuery("
            SELECT r
            FROM AppBundle:GeoPoint AS gp
            INNER JOIN AppBundle:Representative AS r WITH r.geoPointId = gp.id
            WHERE gp.geoCityId = :geoCityId AND r.isActive = true
        ");
        $q->setParameter('geoCityId', $geoCity->getId());
        $geoCity->setGeoPoints($q->getResult());

        return $geoCity;
    }
}
