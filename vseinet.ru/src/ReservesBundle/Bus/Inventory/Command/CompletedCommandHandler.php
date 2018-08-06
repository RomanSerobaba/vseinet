<?php

namespace ReservesBundle\Bus\Inventory\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ReservesBundle\Entity\Inventory;

class CompletedCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(CompletedCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(Inventory::class)->find($command->id);
        if (!$document instanceof Inventory) {
            throw new NotFoundHttpException('Документ не найден (команда)');
        }
        
        if (!empty($document->getRegisteredAt())) {
            throw new ConflictHttpException('Документ проведён (команда)');
        }

        if (!empty($command->completed)) {
            
            $document->setCompletedAt(new \DateTime);
            $document->setCompletedBy($this->get('user.identity')->getUser()->getId());
            $document->setStatus(Inventory::INVENTORY_STATUS_COMPLETED);
            
        }
        else{
            
            $document->setCompletedAt();
            $document->setCompletedBy();
            $document->setStatus(Inventory::INVENTORY_STATUS_STARTED);
            
        }
        
        $em->persist($document);
        $em->flush();
        
    }
    
}
