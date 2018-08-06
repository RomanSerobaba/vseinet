<?php

namespace ReservesBundle\Bus\Inventory\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ReservesBundle\Entity\Inventory;
use ReservesBundle\Entity\InventoryParticipant;

class UpdateCommandHandler extends MessageHandler
{
    
    public function handle(UpdateCommand $command) 
    {
        $currentUser = $this->get('user.identity')->getUser();
        
        $em = $this->getDoctrine()->getManager();
        
        $inventory = $em->getRepository(Inventory::class)->find($command->id);
        if (!$inventory instanceof Inventory) {
            throw new NotFoundHttpException('Документ инвентаризации не найден');
        }

        // Проверка статуса документа
        if (Inventory::INVENTORY_STATUS_COMPLETED == $inventory->getStatus()) {
            throw new ConflictHttpException('Документ закрыт. Изменить документ нельзя.');
        }

        // Проверка статуса документа
        if (Inventory::INVENTORY_STATUS_COMPLETED != $command->status) {
            
            $inventory->setTitle($command->title);
            $inventory->setGeoRoomId($command->geoRoomId);
            $inventory->setResponsibleId($command->responsibleId);
            $inventory->setCategories($command->categories);
            $inventory->setStatus($command->status);
            
            $em->persist($inventory);
            
            $inventoryParticipants = $em->getRepository(InventoryParticipant::class)->findBy(['inventoryDId' => $command->id]);
            if (!empty($inventoryParticipants)) {
                foreach ($inventoryParticipants as $value) {
                    $em->remove($value);
                }
                $em->flush();
            }
            
            foreach ($command->participants as $value) {
                $inventoryParticipant = new InventoryParticipant();
                $inventoryParticipant->setInventoryDId($command->id);
                $inventoryParticipant->setParticipantId($value);
                $em->persist($inventoryParticipant);
            }
            
            $em->flush();
            
            return;            
        }

        // Закрытие документа
        
        $inventory->setTitle($command->title);
        $inventory->setGeoRoomId($command->geoRoomId);
        $inventory->setResponsibleId($command->responsibleId);
        $inventory->setCategories($command->categories);
        $inventory->setStatus($command->status);

        $inventory->setCompletedAt(new \DateTime);

        $em->persist($inventory);
        $em->flush();
    }
    
}
