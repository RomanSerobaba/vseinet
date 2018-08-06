<?php

namespace OrgBundle\Bus\Employee\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\Contact;
use AppBundle\Entity\ContactType;
use AppBundle\Entity\Person;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\Employee;
use OrgBundle\Entity\EmployeeRelative;
use ThirdPartyBundle\Entity\GeoAddress;

class UpdateRelativesCommandHandler extends MessageHandler
{
    public function handle(UpdateRelativesCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();


        /** @var Employee $employee */
        $employee = $em->getRepository(Employee::class)->find($command->id);

        if (!$employee)
            throw new EntityNotFoundException('Сотрудник не найден');


        foreach ($command->relatives as $relativeInfo) {

            /** @var Person $person */
            $person = null;
            if ($relativeInfo->personId > 0)
                $person = $em->getRepository(Person::class)->find($relativeInfo->personId);
            if (!$person)
                $person = new Person();
            $person->setLastname($relativeInfo->lastname);
            $person->setFirstname($relativeInfo->firstname);
            $person->setSecondname($relativeInfo->secondname);
            $em->persist($person);
            $em->flush();


            /** @var Contact $contact */
            $contact = null;
            if ($relativeInfo->mobileId > 0)
                $contact = $em->getRepository(Contact::class)->findOneBy(['id' => $relativeInfo->mobileId, 'personId' => $person->getId()]);
            if (!$contact) {
                $contact = new Contact();
                $contact->setPerson($person);
            }
            $contact->setValue($relativeInfo->mobile);
            $contact->setContactTypeCode(ContactType::CODE_MOBILE);
            $contact->setIsMain(true);
            $em->persist($contact);


            $queryText = '
                SELECT a
                FROM ThirdPartyBundle:GeoAddress AS a
                WHERE a.floor IS NULL AND a.hasLift IS NULL AND a.office IS NULL
                    AND a.geoSubwayStationId IS NULL AND a.coordinates IS NULL
                    AND a.comment IS NULL AND a.address IS NULL';

            $queryValues = [];

            if (!empty($relativeInfo->geoStreetId)) {
                $queryText .= '
                    AND a.geoStreetId = :streetId';
                $queryValues['streetId'] = $relativeInfo->geoStreetId;
            } else {
                $queryText .= '
                    AND a.geoStreetId IS NULL';
            }
            if (!empty($relativeInfo->house)) {
                $queryText .= '
                    AND a.house = :house';
                $queryValues['house'] = $relativeInfo->house;
            } else {
                $queryText .= '
                    AND a.house IS NULL';
            }
            if (!empty($relativeInfo->building)) {
                $queryText .= '
                    AND a.building = :building';
                $queryValues['building'] = $relativeInfo->building;
            } else {
                $queryText .= '
                    AND a.building IS NULL';
            }
            if (!empty($relativeInfo->apartment)) {
                $queryText .= '
                    AND a.apartment = :apartment';
                $queryValues['apartment'] = $relativeInfo->apartment;
            } else {
                $queryText .= '
                    AND a.apartment IS NULL';
            }

            /** @var GeoAddress[] $geoAddress */
            $geoAddress = $em->createQuery($queryText)
                ->setParameters($queryValues)
                ->getResult();

            if (count($geoAddress) > 0) {
                $geoAddress = $geoAddress[0];
            } else {
                $geoAddress = new GeoAddress();
                $geoAddress->setGeoStreetId($relativeInfo->geoStreetId);
                $geoAddress->setHouse($relativeInfo->house);
                $geoAddress->setBuilding($relativeInfo->building);
                $geoAddress->setApartment($relativeInfo->apartment);
                $em->persist($geoAddress);
                $em->flush();
            }


            /** @var EmployeeRelative $relative */
            $relative = null;
            if ($relativeInfo->id > 0)
                $relative = $em->getRepository(EmployeeRelative::class)->find($relativeInfo->id);
            if (!$relative) {
                $relative = new EmployeeRelative();
                $relative->setEmployeeUserId($employee->getUserId());
            }
            $relative->setPersonId($person->getId());
            $relative->setRelation($relativeInfo->relation);
            $relative->setGeoAddressId($geoAddress->getId());
            $em->persist($relative);
            $em->flush();

        }
    }
}