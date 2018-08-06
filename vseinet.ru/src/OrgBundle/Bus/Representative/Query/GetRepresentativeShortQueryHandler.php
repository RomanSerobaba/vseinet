<?php

namespace OrgBundle\Bus\Representative\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\Contact;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\RepresentativeSchedule;

class GetRepresentativeShortQueryHandler extends MessageHandler
{
    public function handle(GetRepresentativeShortQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /** @var DTO\Representative[] $representative */
        $representative = $em->createQuery('
                SELECT
                    NEW OrgBundle\Bus\Representative\Query\DTO\Representative (
                        rep.geoPointId,
                        rep.departmentId,
                        gpoint.code,
                        gpoint.name,
                        gpoint.geoCityId,
                        gaddr.geoStreetId,
                        gaddr.house,
                        gaddr.building,
                        gaddr.floor,
                        rep.isCentral,
                        CASE WHEN rep.type = \'partner\' THEN TRUE ELSE FALSE END,
                        rep.isActive
                    )
                FROM OrgBundle:Representative AS rep
                    LEFT JOIN OrgBundle:GeoPoint AS gpoint
                        WITH rep.geoPointId = gpoint.id
                    LEFT JOIN ThirdPartyBundle:GeoAddress AS gaddr
                        WITH gpoint.geoAddressId = gaddr.id
                WHERE rep.geoPointId = :geoPointId
            ')
            ->setParameter('geoPointId', $query->id)
            ->getResult();

        if (!$representative)
            throw new EntityNotFoundException('Представительство не найдено');

        $representative = $representative[0];


        // Phones
        /** @var Contact[] $phones */
        $phones = $em->createQuery('
                SELECT
                    c
                FROM OrgBundle:RepresentativePhone AS rp
                    INNER JOIN AppBundle:Contact AS c
                        WITH c.id = rp.contactId
                WHERE
                    rp.representativeId = :id
                ORDER BY c.id
            ')
            ->setParameter('id', $representative->id)
            ->getResult();


        $pos = 0;
        foreach ($phones as $phone) {
            if (++$pos > 3)
                break;
            switch ($pos) {
                case 1:
                    $representative->phone1 = $phone->getValue();
                    break;
                case 2:
                    $representative->phone2 = $phone->getValue();
                    break;
                case 3;
                    $representative->phone3 = $phone->getValue();
                    break;
            }
        }


        // Schedules
        /** @var RepresentativeSchedule[] $shedule */
        $shedule = $em->createQuery('
                SELECT rs
                FROM OrgBundle:RepresentativeSchedule AS rs
                WHERE rs.representativeId = :id
            ')
            ->setParameter('id', $representative->id)
            ->getResult();

        if (count($shedule) > 0) {
            $shedule = $shedule[0];
            $representative->since1 = $shedule->getS1();
            $representative->till1 = $shedule->getT1();
            $representative->since2 = $shedule->getS2();
            $representative->till2 = $shedule->getT2();
            $representative->since3 = $shedule->getS3();
            $representative->till3 = $shedule->getT3();
            $representative->since4 = $shedule->getS4();
            $representative->till4 = $shedule->getT4();
            $representative->since5 = $shedule->getS5();
            $representative->till5 = $shedule->getT5();
            $representative->since6 = $shedule->getS6();
            $representative->till6 = $shedule->getT6();
            $representative->since7 = $shedule->getS7();
            $representative->till7 = $shedule->getT7();
        }



        /** @var \GeoBundle\Service\DTO\StreetInfo[] $street */
        $street = $em->createQuery('
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
            ->setParameter('streetId', $representative->streetId)
            ->getResult();

        if (count($street) > 0)
            $representative->streetInfo = $street[0];



        /** @var \GeoBundle\Bus\Cities\Query\DTO\CityInfo[] $city */
        $city = $em->createQuery('
                SELECT
                    NEW GeoBundle\Bus\Cities\Query\DTO\CityInfo (
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
            ->setParameter('cityId', $representative->cityId)
            ->getResult();

        if (count($city) > 0)
            $representative->cityInfo = $city[0];

        return $representative;
    }
}