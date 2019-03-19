<?php

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Entity\GeoCity;
use AppBundle\Entity\GeoStreet;
use AppBundle\Entity\GeoAddress;
use AppBundle\Entity\GeoAddressToPerson;

class AddAddressCommandHandler extends MessageHandler
{
    public function handle(AddAddressCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $geoCity = $em->getRepository(GeoCity::class)->find($command->geoCityId);
        if (!$geoCity instanceof GeoCity || $geoCity->getName() != $command->geoCityName) {
            throw new NotFoundHttpException('Город не найден');
        }

        $geoStreet = $em->getRepository(GeoStreet::class)->find($command->geoStreetId);
        if (!$geoStreet instanceof GeoStreet || $geoStreet->getName() != $command->geoStreetName) {
            throw new NotFoundHttpException('Улица не найдена');
        }

        if ($command->id) {
            $geoAddress = $em->getRepository(GeoAddress::class)->find($command->id);
            if (!$geoAddress instanceof GeoAddress) {
                throw new BadRequestHttpException();
            }
        } else {
            $geoAddress = new GeoAddress();
        }

        $geoAddress->setGeoCityId($geoCity->getId());
        $geoAddress->setGeoStreetId($geoStreet->getId());
        $geoAddress->setHouse($command->house);
        $geoAddress->setBuilding($command->building);
        $geoAddress->setApartment($command->apartment);
        $geoAddress->setFloor($command->floor);
        $geoAddress->setHasLift($command->hasLift);
        $geoAddress->setComment($command->comment);

        $em->persist($geoAddress);
        $em->flush($geoAddress);

        if ($command->isMain) {
            $q = $em->createQuery('
                UPDATE AppBundle:GeoAddressToPerson ga2p
                SET ga2p.isMain = false
                WHERE ga2p.geoAddressId != :geoAddressId AND ga2p.personId = :personId
            ');
            $q->setParameter('geoAddressId', $geoAddress->getId());
            $q->setParameter('personId', $this->getUser()->getPersonId());
            $q->setParameter('isMain', $command->isMain);
        }

        $ga2p = $em->getRepository(GeoAddressToPerson::class)->findOneBy([
            'geoAddressId' => $geoAddress->getId(),
            'personId' => $this->getUser()->getPersonId(),
        ]);
        if (!$ga2p instanceof GeoAddressToPerson) {
            $ga2p = new GeoAddressToPerson();
            $ga2p->setGeoAddressId($geoAddress->getId());
            $ga2p->setPersonId($this->getUser()->getPersonId());
        }
        $ga2p->setIsMain($command->isMain);

        $em->persist($ga2p);
        $em->flush();

        return $geoAddress;
    }
}
