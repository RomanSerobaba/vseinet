<?php

namespace ReservesBundle\Bus\Inventory\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\Entity\Inventory;
use ReservesBundle\Entity\InventoryParticipant;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {

        $currentUser = $this->get('user.identity')->getUser();

        $em = $this->getDoctrine()->getManager();

        // Получаем номер следующей инвентаризации
        
        $queryText = "select nextval('inventory_id_seq'::regclass) as next_number;";
                
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('next_number', 'next_number', 'integer');
        
        $queryDB = $em->createNativeQuery($queryText, $rsm);

        $inventoryNumber = $queryDB->getResult()[0]['next_number'];
        
        //
        
        $item = new Inventory();

        $item->setCreatedAt(new \DateTime());
        $item->setNumber($inventoryNumber);
        $item->setCreatedBy($currentUser->getId());
        $item->setStatus(Inventory::INVENTORY_STATUS_CREATED);
        $item->setGeoRoomId($command->geoRoomId);
        $item->setResponsibleId($command->responsibleId);
        $item->setCategories($command->categories);
        $item->setStatus($command->status);
        if (empty($command->title)) {
            $dateStr = $item->getCreatedAt()->format("d.m.Y");
            $item->setTitle("Инвентаризация №{$item->getNumber()} от {$dateStr}");
        }else{
            $item->setTitle($command->title);
        }

        $em->persist($item);
        $em->flush();

        foreach ($command->participants as $value) {
            $inventoryParticipant = new InventoryParticipant();
            $inventoryParticipant->setInventoryDId($item->getDId());
            $inventoryParticipant->setParticipantId($value);
            $em->persist($inventoryParticipant);
        }
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $item->getDId());
    }
}