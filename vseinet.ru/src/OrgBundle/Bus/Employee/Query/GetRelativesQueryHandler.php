<?php

namespace OrgBundle\Bus\Employee\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\ContactType;

class GetRelativesQueryHandler extends MessageHandler
{
    public function handle(GetRelativesQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        // Select Relatives
        /** @var DTO\Relative[] $relatives */
        $relatives = $em->createQuery('
                SELECT
                    NEW OrgBundle\Bus\Employee\Query\DTO\Relative (
                        er.id,
                        er.employeeUserId,
                        er.relation,
                        er.personId,
                        pp.lastname,
                        pp.firstname,
                        pp.secondname,
                        pp.gender,
                        pp.birthday,
                        er.geoAddressId
                    )
                FROM
                    OrgBundle:EmployeeRelative AS er
                    LEFT JOIN AppBundle:Person AS pp
                        WITH er.personId = pp.id
                WHERE er.employeeUserId = :userId
                ORDER BY er.id
            ')
            ->setParameter('userId', $query->id)
            ->getResult();


        $personIds = [];
        $addressIds = [];
        foreach ($relatives as $index => $relative) {
            if ($relative->personId)
                $personIds[$index] = $relative->personId;
            if ($relative->addressId)
                $addressIds[$index] = $relative->addressId;
        }


        // Add mobile phones info
        /** @var \AppBundle\Bus\User\Query\DTO\Contact[] $contacts */
        $contacts = $em->createQuery('
                SELECT
                    NEW AppBundle\Bus\User\Query\DTO\Contact (
                        c.id,
                        c.personId,
                        c.contactTypeCode,
                        c.value,
                        c.shortValue,
                        c.comment,
                        c.cityId,
                        c.isMain
                    )
                FROM AppBundle:Contact AS c
                WHERE c.personId IN (:personIds)
                ORDER BY c.personId, c.isMain DESC
            ')
            ->setParameter('personIds', $personIds)
            ->getResult();

        $personIds = array_flip($personIds);

        foreach ($contacts as $contact) {
            if (!isset($relatives[$personIds[$contact->personId]]))
                continue;

            $relative = $relatives[$personIds[$contact->personId]];

            if ($contact->contactTypeCode == ContactType::CODE_MOBILE) {
                $relative->mobile = $contact->value;
                $relative->mobileId = $contact->id;
            }
        }


        // Add address info
        /** @var \AppBundle\Bus\User\Query\DTO\Address[] $addresses */
        $addresses = $em->createQuery('
                SELECT
                    NEW AppBundle\Bus\User\Query\DTO\Address (
                        a.id,
                        a.geoStreetId,
                        a.house,
                        a.building,
                        a.apartment,
                        a.floor,
                        a.hasLift,
                        a.office,
                        a.geoSubwayStationId,
                        a.coordinates,
                        a.comment,
                        a.address
                    )
                FROM ThirdPartyBundle:GeoAddress AS a
                WHERE a.id IN ( :addressId )
            ')
            ->setParameter('addressId', $addressIds)
            ->getResult();

        if (count($addresses) < 1)
            return $relatives;


        $addressIds = array_flip($addressIds);
        $streetIds = [];

        foreach ($addresses as $address) {
            if (!isset($relatives[$addressIds[$address->id]]))
                continue;

            $relative = $relatives[$addressIds[$address->id]];
            if ($relative->address === null) {
                $relative->address = $address;
                $streetIds[$addressIds[$address->id]] = $address->geoStreetId;
            }
        }


        // Add street info to addresses
        /** @var \GeoBundle\Service\DTO\StreetInfo[] $streets */
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
                WHERE gstreet.id IN (:streetId)
            ')
            ->setParameter('streetId', $streetIds)
            ->getResult();

        foreach ($streets as $street) {
            foreach ($streetIds as $relativeId => $streetId) {
                if ($street->streetId == $streetId) {
                    $relative = $relatives[$relativeId];
                    $relative->address->geoStreet = $street;
                }
            }
        }

        return $relatives;
    }
}