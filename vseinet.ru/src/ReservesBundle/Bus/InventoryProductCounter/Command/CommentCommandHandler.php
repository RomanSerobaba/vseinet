<?php

namespace ReservesBundle\Bus\InventoryProductCounter\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ReservesBundle\Entity\InventoryProductCounter;
use ReservesBundle\Entity\Inventory;

class CommentCommandHandler extends MessageHandler
{
    public function handle(CommentCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $inventory = $em->getRepository(Inventory::class)->find($command->inventoryId);
        
        if (!$inventory instanceof Inventory) {
            throw new NotFoundHttpException('Документ инвентаризации не найден');
        }
        
        $item = $em->getRepository(InventoryProductCounter::class)->findOneBy([
            'inventoryDId' => $command->inventoryId, 
            'participantId' => $this->get('user.identity')->getUser()->getId(),
            'baseProductId' => $command->id
        ]);
        
        if ($item instanceof InventoryProductCounter) {
            $item->setComment($command->comment);
        }else{
            $item = new InventoryProductCounter();
            $item->fill($command);
        }
        
        $em->persist($item);
        $em->flush();
    }
}