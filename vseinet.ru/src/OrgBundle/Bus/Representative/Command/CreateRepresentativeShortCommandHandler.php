<?php

namespace OrgBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\Contact;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\Department;
use OrgBundle\Entity\GeoPoint;
use OrgBundle\Entity\Representative;
use OrgBundle\Entity\RepresentativePhone;
use OrgBundle\Entity\RepresentativeSchedule;
use ThirdPartyBundle\Entity\GeoAddress;

class CreateRepresentativeShortCommandHandler extends MessageHandler
{
    public function handle(CreateRepresentativeShortCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        /** @var Department $department */
        $department = $em->getRepository(Department::class)->find($command->departmentId);

        if (!$department)
            throw new EntityNotFoundException('Подразделение не найдено');


        $queryText = '
            SELECT a
            FROM ThirdPartyBundle:GeoAddress AS a
            WHERE a.apartment IS NULL AND a.hasLift IS NULL AND a.office IS NULL
                AND a.geoSubwayStationId IS NULL AND a.coordinates IS NULL
                AND a.comment IS NULL AND a.address IS NULL';

        $queryValues = [];

        if (!empty($command->streetIdt)) {
            $queryText .= '
                AND a.geoStreetId = :streetId';
            $queryValues['streetId'] = $command->streetId;
        } else {
            $queryText .= '
                AND a.geoStreetId IS NULL';
        }
        if (!empty($command->house)) {
            $queryText .= '
                AND a.house = :house';
            $queryValues['house'] = $command->house;
        } else {
            $queryText .= '
                AND a.house IS NULL';
        }
        if (!empty($command->building)) {
            $queryText .= '
                AND a.building = :building';
            $queryValues['building'] = $command->building;
        } else {
            $queryText .= '
                AND a.building IS NULL';
        }
        if (!empty($command->floor)) {
            $queryText .= '
                AND a.floor = :floor';
            $queryValues['floor'] = $command->floor;
        } else {
            $queryText .= '
                AND a.floor IS NULL';
        }

        /** @var GeoAddress[] $geoAddress */
        $geoAddress = $em->createQuery($queryText)
            ->setParameters($queryValues)
            ->getResult();

        if (count($geoAddress) > 0) {
            $geoAddress = $geoAddress[0];
        } else {
            $geoAddress = new GeoAddress();
            $geoAddress->setGeoStreetId($command->streetId);
            $geoAddress->setHouse($command->house);
            $geoAddress->setBuilding($command->building);
            $geoAddress->setFloor($command->floor);
        }
        $em->persist($geoAddress);
        $em->flush();


        /** @var GeoPoint $geoPoint */
        $geoPoint = new GeoPoint();
        $geoPoint->setCode($command->code);
        $geoPoint->setName($command->name);
        $geoPoint->setGeoCityId($command->cityId);
        $geoPoint->setGeoAddressId($geoAddress->getId());
        $em->persist($geoPoint);
        $em->flush();


        /** @var Representative $representative */
        $representative = new Representative();
        $representative->setGeoPointId($geoPoint->getId());
        $representative->setDepartmentId($department->getId());
        $representative->setIsCentral($command->isCentral);
        $representative->setIsActive($command->isActive);
        $representative->setType($command->isPartner ? 'partner' : 'our');
        $representative->setHasWarehouse(false);
        $representative->setHasRetail(false);
        $representative->setHasOrderIssueing(false);
        $representative->setHasDelivery(false);
        $representative->setHasRising(false);
        $representative->setHasTransit(false);
        $em->persist($representative);


        // Phones
        for ($pos = 1; $pos <= 3; ++$pos) {
            $phone = new Contact();
            $phone->setCityId($command->cityId);

            switch ($pos) {
                case 1:
                    $phone->setValue($command->phone1);
                    break;
                case 2:
                    $phone->setValue($command->phone2);
                    break;
                case 3;
                    $phone->setValue($command->phone3);
                    break;
            }

            $em->persist($phone);
            $em->flush();

            $phoneRep = new RepresentativePhone();
            $phoneRep->setRepresentativeId($representative->getGeoPointId());
            $phoneRep->setContactId($phone->getId());
            $em->persist($phoneRep);
        }


        // Schedules
        /** @var RepresentativeSchedule $shedule */
        $shedule = new RepresentativeSchedule();
        $shedule->setRepresentativeId($representative->getGeoPointId());
        $shedule->setCreatedBy($currentUser->getId());
        $shedule->setCreatedAt(new \DateTime());

        $shedule->setS1($command->since1 instanceof \DateTime ? $command->since1 : new \DateTime($command->since1));
        $shedule->setT1($command->till1  instanceof \DateTime ? $command->till1  : new \DateTime($command->till1 ));
        $shedule->setS2($command->since2 instanceof \DateTime ? $command->since2 : new \DateTime($command->since2));
        $shedule->setT2($command->till2  instanceof \DateTime ? $command->till2  : new \DateTime($command->till2 ));
        $shedule->setS3($command->since3 instanceof \DateTime ? $command->since3 : new \DateTime($command->since3));
        $shedule->setT3($command->till3  instanceof \DateTime ? $command->till3  : new \DateTime($command->till3 ));
        $shedule->setS4($command->since4 instanceof \DateTime ? $command->since4 : new \DateTime($command->since4));
        $shedule->setT4($command->till4  instanceof \DateTime ? $command->till4  : new \DateTime($command->till4 ));
        $shedule->setS5($command->since5 instanceof \DateTime ? $command->since5 : new \DateTime($command->since5));
        $shedule->setT5($command->till5  instanceof \DateTime ? $command->till5  : new \DateTime($command->till5 ));
        $shedule->setS6($command->since6 instanceof \DateTime ? $command->since6 : new \DateTime($command->since6));
        $shedule->setT6($command->till6  instanceof \DateTime ? $command->till6  : new \DateTime($command->till6 ));
        $shedule->setS7($command->since7 instanceof \DateTime ? $command->since7 : new \DateTime($command->since7));
        $shedule->setT7($command->till7  instanceof \DateTime ? $command->till7  : new \DateTime($command->till7 ));

        $em->persist($shedule);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $representative->getGeoPointId());
    }
}