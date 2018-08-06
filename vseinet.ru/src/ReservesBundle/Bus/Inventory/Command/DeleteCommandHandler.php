<?php

namespace ReservesBundle\Bus\Inventory\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ReservesBundle\Entity\Inventory;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $item = $em->getRepository(Inventory::class)->find($command->id);
        if (!$item instanceof Inventory) {
            throw new NotFoundHttpException('Документ инвентаризации не найден (команда)');
        }
        
        $em->remove($item);
        $em->flush();
    }
}