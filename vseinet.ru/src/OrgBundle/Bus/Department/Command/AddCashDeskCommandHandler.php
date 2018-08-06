<?php

namespace OrgBundle\Bus\Department\Command;

use AppBundle\Bus\Message\MessageHandler;
use OrgBundle\Entity\CashDesk;

class AddCashDeskCommandHandler extends MessageHandler
{
    public function handle(AddCashDeskCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $cashDesk = new CashDesk();
        $cashDesk->setDepartmentId($command->id);
        $cashDesk->setTitle($command->title);
        $cashDesk->setGeoRoomId($command->geoRoomId);
        if ($command->collectorId)
            $cashDesk->setCollectorId($command->collectorId);
        $em->persist($cashDesk);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $cashDesk->getId());
    }
}