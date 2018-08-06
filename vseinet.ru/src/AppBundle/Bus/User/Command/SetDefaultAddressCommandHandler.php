<?php

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\Entity\UserToAddress;
use Doctrine\ORM\EntityNotFoundException;
use ThirdPartyBundle\Entity\GeoAddress;

class SetDefaultAddressCommandHandler extends MessageHandler
{
    public function handle(SetDefaultAddressCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();


        /** @var User $user */
        $user = $em->getRepository(User::class)->find($command->id);

        if (!$user)
            throw new EntityNotFoundException("User not found");


        $queryText = '
            SELECT
                a AS address,
                ua.userId,
                ua.isDefault
            FROM ThirdPartyBundle:GeoAddress AS a
                LEFT JOIN AppBundle:UserToAddress AS ua
                    WITH a.id = ua.geoAddressId AND ua.userId = :userId
            WHERE a.geoStreetId = :streetId AND a.house = :house';

        $queryValues = [
            'userId' => $user->getId(),
            'streetId' => $command->geoStreetId,
            'house' => $command->house
        ];

        if (!empty($command->building)) {
            $queryText .= '
                AND a.building = :building';
            $queryValues['building'] = $command->building;
        }
        if (!empty($command->apartment)) {
            $queryText .= '
                AND a.apartment = :apartment';
            $queryValues['apartment'] = $command->apartment;
        }
        if (!empty($command->floor)) {
            $queryText .= '
                AND a.floor = :floor';
            $queryValues['floor'] = $command->floor;
        }
        if (!empty($command->hasLift)) {
            $queryText .= '
                AND a.hasLift = :hasLift';
            $queryValues['hasLift'] = $command->hasLift;
        }
        if (!empty($command->office)) {
            $queryText .= '
                AND a.office = :office';
            $queryValues['office'] = $command->office;
        }
        if (!empty($command->geoSubwayStationId)) {
            $queryText .= '
                AND a.geoSubwayStationId = :geoSubwayStationId';
            $queryValues['geoSubwayStationId'] = $command->geoSubwayStationId;
        }
        if (!empty($command->coordinates)) {
            $queryText .= '
                AND a.coordinates = :coordinates';
            $queryValues['coordinates'] = $command->coordinates;
        }
        if (!empty($command->comment)) {
            $queryText .= '
                AND a.comment = :comment';
            $queryValues['comment'] = $command->comment;
        }
        if (!empty($command->address)) {
            $queryText .= '
                AND a.address = :address';
            $queryValues['address'] = $command->address;
        }

        /** @var array $addresses */
        $addresses = $em->createQuery($queryText)
            ->setParameters($queryValues)
            ->getResult();


        /** @var GeoAddress $address */
        $address = null;
        $isLinked = false;
        $isDefault = false;

        foreach ($addresses as $addr) {
            if (($address === null) || $addr['isDefault']) {
                $address = $addr['address'];
                $isDefault = $addr['isDefault'] ? true : false;

                if ($addr['userId'])
                    $isLinked = true;
            }
        }

        if ($isDefault)
            return;

        /** @var UserToAddress[] $userToAddresses */
        $userToAddresses = $em->getRepository(UserToAddress::class)->findBy(['userId' => $user->getId()]);

        foreach ($userToAddresses as $uta) {
            if ($address && ($uta->getGeoAddressId() == $address->getId())) {
                $uta->setIsDefault(true);
            } else {
                $uta->setIsDefault(false);
            }
        }

        if ($isLinked) {
            $em->flush();
            return;
        }

        if (!$address) {
            $address = new GeoAddress();
            $address->setGeoStreetId($command->geoStreetId);
            $address->setHouse($command->house);
            $address->setBuilding($command->building);
            $address->setApartment($command->apartment);
            $address->setFloor($command->floor);
            $address->setHasLift($command->hasLift);
            $address->setOffice($command->office);
            $address->setGeoSubwayStationId($command->geoSubwayStationId);
            $address->setCoordinates($command->coordinates);
            $address->setComment($command->comment);
            $address->setAddress($command->address);
            $em->persist($address);
            $em->flush();
        }

        if ($address) {
            $userToAddress = new UserToAddress();
            $userToAddress->setUserId($user->getId());
            $userToAddress->setGeoAddressId($address->getId());
            $userToAddress->setIsDefault(true);
            $em->persist($userToAddress);
        }
        $em->flush();
    }
}