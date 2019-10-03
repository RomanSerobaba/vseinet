<?php

namespace AppBundle\Service;

use AppBundle\Container\ContainerAware;
use AppBundle\Entity\GeoCity;
use Doctrine\ORM\Query\ResultSetMapping;

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

    /**
     * @param int $userId
     */
    public function setEmployeeGeoCity($userId): void
    {
        $data = $this->getDoctrine()->getManager()->createNativeQuery('
            SELECT
                p.geo_city_id
            FROM org_employment_history AS eh
            INNER JOIN org_employee_to_geo_room AS e2r ON e2r.org_employee_user_id = eh.org_employee_user_id
            INNER JOIN geo_room AS r ON r.id = e2r.geo_room_id
            INNER JOIN geo_point AS p ON p.id = r.geo_point_id
            WHERE eh.org_employee_user_id = :user_id AND eh.hired_at IS NOT NULL AND eh.fired_at IS NULL AND e2r.is_main = TRUE
        ', (new ResultSetMapping())->addScalarResult('geo_city_id', 'geoCityId', 'integer'))
            ->setParameter('user_id', $userId)
            ->getOneOrNullResult();

        if ($data) {
            $session = $this->get('request_stack')->getMasterRequest()->getSession();
            $geoCity = $session->get('geo_city');
            if ($geoCity->getId() !== $data['geoCityId']) {
                $geoCity = $this->loadGeoCity($data['geoCityId']);
                $session->set('geo_city', $geoCity);
            }
        }
    }

    protected function detectGeoCityId(): int
    {
        $em = $this->getDoctrine()->getManager();

        $token = $this->get('security.token_storage')->getToken();
        if (null !== $token) {
            $user = $token->getUser();
            if (is_object($user)) {
                $data = $em->createNativeQuery('
                    SELECT
                        p.geo_city_id
                    FROM org_employment_history AS eh
                    INNER JOIN org_employee_to_geo_room AS e2r ON e2r.org_employee_user_id = eh.org_employee_user_id
                    INNER JOIN geo_room AS r ON r.id = e2r.geo_room_id
                    INNER JOIN geo_point AS p ON p.id = r.geo_point_id
                    WHERE eh.org_employee_user_id = :user_id AND eh.hired_at IS NOT NULL AND eh.fired_at IS NULL AND e2r.is_main = TRUE
                ', (new ResultSetMapping())->addScalarResult('geo_city_id', 'geoCityId', 'integer'))
                    ->setParameter('user_id', $user->getId())
                    ->getOneOrNullResult();

                if ($data) {
                    return $data['geoCityId'];
                }

                $geoCityId = $user->getGeoCityId();
                if ($geoCityId) {
                    return $geoCityId;
                }
                $q = $em->createQuery('
                    SELECT ga.geoCityId, CASE WHEN ga2p.isMain = TRUE THEN 1 ELSE 2 END AS HIDDEN ORD
                    FROM AppBundle:GeoAddress AS ga
                    INNER JOIN AppBundle:GeoAddressToPerson AS ga2p WITH ga2p.geoAddressId = ga.id
                    WHERE ga2p.personId = :personId AND ga.geoCityId IS NOT NULL
                    ORDER BY ORD
                ');
                $q->setParameter('personId', $user->getPersonId());
                $q->setMaxResults(1);
                $geoCityId = $q->getOneOrNullResult();
                if ($geoCityId) {
                    return $geoCityId;
                }
            }
        }
        $q = $em->createQuery('
            SELECT
                gic.geoCityId
            FROM AppBundle:GeoIp AS gi
            INNER JOIN AppBundle:GeoIpCity AS gic WITH gic.id = gi.geoIpCityId
            WHERE :longIp BETWEEN gi.longIp1 AND gi.longIp2
        ');
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

        $q = $em->createQuery('
            SELECT r
            FROM AppBundle:GeoPoint AS gp
            INNER JOIN AppBundle:Representative AS r WITH r.geoPointId = gp.id
            WHERE gp.geoCityId = :geoCityId AND r.isActive = true
        ');
        $q->setParameter('geoCityId', $geoCity->getId());
        $geoCity->setGeoPoints($q->getResult());

        return $geoCity;
    }
}
