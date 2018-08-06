<?php

namespace OrgBundle\Bus\Department\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\GeoRoom;
use OrgBundle\Entity\Representative;

class AddWarehouseCommandHandler extends MessageHandler
{
    public function handle(AddWarehouseCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /** @var Representative $representative */
        $representative = $em->getRepository(Representative::class)->findOneBy(['departmentId' => $command->id]);

        if (!$representative)
            throw new EntityNotFoundException('Представительство не найдено');


        $room = new GeoRoom();
        $room->setGeoPointId($representative->getGeoPointId());
        $room->setName($command->name);
        $room->setType($command->type);

        $em->persist($room);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $room->getId());
    }
}