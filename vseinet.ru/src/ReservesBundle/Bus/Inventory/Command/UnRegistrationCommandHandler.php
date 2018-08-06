<?php

namespace ReservesBundle\Bus\Inventory\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
USE Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ReservesBundle\Entity\Inventory;
use ReservesBundle\Entity\GoodsIssueRegister;

class UnRegistrationCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(UnRegistrationCommand $command) 
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(Inventory::class)->find($command->id);
        if (!$document instanceof Inventory) {
            throw new NotFoundHttpException('Документ не найден (команда)');
        }
        
        if (empty($document->getRegisteredAt())) {
            return;
        }

        // Отмена проведения документа
        
        // Запись шапки документа

        $document->setRegisteredAt();
        $document->setRegisteredBy();

        $em->persist($document);
        $em->flush();
        
    }
    
}
