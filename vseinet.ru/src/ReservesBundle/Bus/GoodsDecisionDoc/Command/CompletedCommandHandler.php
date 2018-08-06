<?php

namespace ReservesBundle\Bus\GoodsDecisionDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ReservesBundle\Entity\GoodsDecisionDoc;

class CompletedCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(CompletedCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(GoodsDecisionDoc::class)->find($command->id);
        if (!$document instanceof GoodsDecisionDoc) {
            throw new NotFoundHttpException('Документ не найден (команда)');
        }
        
        if (!empty($document->getRegisteredAt())) {
            throw new ConflictHttpException('Документ проведён (команда)');
        }

        if (!empty($command->completed)) {
            
            $document->setCompletedAt(new \DateTime);
            $document->setCompletedBy($this->get('user.identity')->getUser()->getId());
            
        }
        else{
            
            $document->setCompletedAt();
            $document->setCompletedBy();
            
        }
        
        $em->persist($document);
        $em->flush();
        
    }
    
}
