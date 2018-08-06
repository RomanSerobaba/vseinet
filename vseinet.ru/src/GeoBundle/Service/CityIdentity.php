<?php 

namespace GeoBundle\Service;

use AppBundle\Container\ContainerAware;

class CityIdentity extends ContainerAware
{
    public function getId(): int
    {
        // @development
        return 1;

        $request = $this->get('request_stack')->getMasterRequest();
        $session = $request->getSession();

        $geoCityId = $session->get('geo_city_id');
        if (null === $geoCityId) {

            $em = $this->getDoctrine()->getManager();

            if ($this->get('user.identity')->isAuthorized()) {
                $q = $em->createQuery("
                    SELECT 
                        u.cityId
                    FROM AppBundle:User u 
                    WHERE u.id = :id
                ");
                $q->setParameter('id', $this->get('user.identity')->getUser()->getId());
                $geoCityId = $q->getSingleScalarResult();
            }

            if (null === $geoCityId) {
                $q = $em->createQuery("
                    SELECT 
                        gi.geoCityId 
                    FROM GeoBundle:GeoIp gi 
                    WHERE :longIp BETWEEN gi.longIp1 AND gi.longIp2 
                ");
                $q->setParameter('longIp', ip2long($request->getClientIp()));
                $q->setMaxResults(1);
                $result = $q->getOneOrNullResult();
                $geoCityId = $result ? $result['geoCityId'] : 0;
            }
            
            $session->set('geo_city_id', $geoCityId);
        }

        return (int) $geoCityId;
    }

    public function invalidate(): void
    {
        $this->get('request_stack')->getMasterRequest()->getSession()->remove('geo_city_id');
    }

    /**
     * @param int $id
     * @return DTO\RegionInfo
     */
    public function getRegionInfo(int $id) : DTO\RegionInfo
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var DTO\RegionInfo[] $regions */
        $regions = $em->createQuery('
                SELECT
                    NEW GeoBundle\Service\DTO\RegionInfo (
                        gregion.id,
                        gregion.name,
                        gregion.unit,
                        gregion.AOGUID
                    )
                FROM ThirdPartyBundle:GeoRegion AS gregion
                WHERE gregion.id = :regionId
            ')
            ->setParameter('regionId', $id)
            ->getResult();

        if (count($regions) <= 0)
            return null;

        return $regions[0];
    }

    /**
     * @return DTO\Region[]
     */
    public function searchRegions(string $sText=null) : array
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $qText = 'SELECT
                    NEW GeoBundle\Service\DTO\Region (
                        MIN( gs.id ),
                        CONCAT ( gs.unit, \' \', gs.name )
                    )
                FROM ThirdPartyBundle:GeoRegion gs';
        $qAttr = [];

        if ($sText) {
            $qText .= '
                WHERE LOWER(gs.name) LIKE :searchName';
            $qAttr['searchName'] = mb_strtolower($sText) . '%';
        }

        $qText .= '
                GROUP BY gs.unit, gs.name
                ORDER BY gs.name';

        $q = $em->createQuery($qText)
            ->setParameters($qAttr);

        return $q->getResult();
    }

    /**
     * @param int $id
     * @return DTO\CityInfo
     */
    public function getCityInfo(int $id) : DTO\CityInfo
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var DTO\CityInfo[] $cities */
        $cities = $em->createQuery('
                SELECT
                    NEW GeoBundle\Service\DTO\CityInfo (
                        gregion.id,
                        gregion.name,
                        gregion.unit,
                        gregion.AOGUID,

                        gcity.id,
                        gcity.name,
                        gcity.unit,
                        gcity.isCentral,
                        gcity.phoneCode,
                        gcity.AOGUID
                    )
                FROM ThirdPartyBundle:GeoCity AS gcity
                    LEFT JOIN ThirdPartyBundle:GeoRegion AS gregion
                        WITH gcity.geoRegionId = gregion.id
                WHERE gcity.id = :cityId
            ')
            ->setParameter('cityId', $id)
            ->getResult();

        if (count($cities) <= 0)
            return null;

        return $cities[0];
    }

    /**
     * @param string $sText
     * @param int|null $regionId
     * @param int $limit
     * @return DTO\City[]
     */
    public function searchCity(string $sText, int $regionId = null, int $limit = 20) : array
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $qText = 'SELECT
                    NEW GeoBundle\Service\DTO\City (
                        MIN( gs.id ),
                        CONCAT ( gs.unit, \' \', gs.name )
                    )
                FROM ThirdPartyBundle:GeoCity gs
                WHERE LOWER(gs.name) LIKE :searchName';
        $qAttr = [
            'searchName' => mb_strtolower($sText).'%'
        ];

        if ($regionId && $regionId > 0) {
            $qText .= ' AND gs.geoRegionId = :regionId';
            $qAttr['regionId'] = $regionId;
        }

        $qText .= '
                GROUP BY gs.unit, gs.name
                ORDER BY gs.name';

        $q = $em->createQuery($qText)
            ->setFirstResult(0)
            ->setMaxResults($limit)
            ->setParameters($qAttr);

        return $q->getResult();
    }

    /**
     * @param int $id
     * @return DTO\StreetInfo
     */
    public function getStreetInfo(int $id) : DTO\StreetInfo
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var DTO\StreetInfo[] $streets */
        $streets = $em->createQuery('
                SELECT
                    NEW GeoBundle\Service\DTO\StreetInfo (
                        gregion.id,
                        gregion.name,
                        gregion.unit,
                        gregion.AOGUID,

                        gcity.id,
                        gcity.name,
                        gcity.unit,
                        gcity.isCentral,
                        gcity.phoneCode,
                        gcity.AOGUID,

                        gstreet.id,
                        gstreet.name,
                        gstreet.unit,
                        gstreet.AOGUID
                    )
                FROM ThirdPartyBundle:GeoStreet AS gstreet
                    LEFT JOIN ThirdPartyBundle:GeoCity AS gcity
                        WITH gstreet.geoCityId = gcity.id
                    LEFT JOIN ThirdPartyBundle:GeoRegion AS gregion
                        WITH gcity.geoRegionId = gregion.id
                WHERE gstreet.id = :streetId
            ')
            ->setParameter('streetId', $id)
            ->getResult();

        if (count($streets) <= 0)
            return null;

        return $streets[0];
    }

    /**
     * @param string $sText
     * @param int $cityId
     * @param int $limit
     * @return DTO\Street[]
     */
    public function searchStreet(string $sText, int $cityId, int $limit = 20) : array
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery(
            "SELECT
                    NEW GeoBundle\Service\DTO\Street (
                        MIN( gs.id ),
                        CONCAT ( gs.unit, ' ', gs.name )
                    )
                FROM ThirdPartyBundle:GeoStreet gs
                WHERE gs.geoCityId = :cityId AND LOWER(gs.name) LIKE :searchName
                GROUP BY gs.unit, gs.name
                ORDER BY gs.name")
            ->setFirstResult(0)
            ->setMaxResults($limit)

            ->setParameter('cityId', $cityId)
            ->setParameter('searchName', mb_strtolower($sText).'%');

        return $q->getResult();
    }
}
