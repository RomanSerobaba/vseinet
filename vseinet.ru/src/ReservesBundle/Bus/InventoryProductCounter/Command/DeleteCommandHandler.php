<?php

namespace ReservesBundle\Bus\InventoryProductCounter\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ReservesBundle\Entity\InventoryProductCounter;
use ReservesBundle\Entity\Inventory;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $item = $em->getRepository(InventoryProductCounter::class)->findOneBy([
            'inventoryDId' => $command->inventoryId, 
            'baseProductId' => $command->id,
            'participantId' => $this->get('user.identity')->getUser()->getId()
        ]);
        
        if (!$item instanceof InventoryProductCounter) {
            throw new NotFoundHttpException('Подсчеты участника инвентаризации не найдены');
        }
        
        $inventory = $em->getRepository(Inventory::class)->find($command->inventoryId);
        
        if (!$inventory instanceof Inventory) {
            throw new NotFoundHttpException('Документ инвентаризации не найден');
        }
        
        // Проверка статуса документа
        if (Inventory::INVENTORY_STATUS_STARTED != $inventory->getStatus()) {
            throw new ConflictHttpException('При данном статусе инвентаризации внесение остатков не возможно.');
        }

        $em->remove($item);
        $em->flush();
    }
}