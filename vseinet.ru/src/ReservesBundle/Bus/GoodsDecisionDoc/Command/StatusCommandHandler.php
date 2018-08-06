<?php

namespace ReservesBundle\Bus\GoodsDecisionDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ReservesBundle\Entity\GoodsDecisionDoc;
use ReservesBundle\Bus\GoodsDecisionDoc\GoodsDecisionDocRegistration;
use ReservesBundle\Bus\GoodsDecisionDoc\GoodsDecisionDocUnRegistration;
use ReservesBundle\Bus\GoodsDecisionDoc\GoodsDecisionDocUpdate;

class StatusCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(StatusCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(GoodsDecisionDoc::class)->find($command->id);
        if (!$document instanceof GoodsDecisionDoc) {
            throw new NotFoundHttpException('Документ не найден.');
        }

        if ($document->getStatusCode() == $command->statusCode) return; // Проверка наличия изменений
        
        $currentUser = $this->get('user.identity')->getUser();
        
        GoodsDecisionDocUnRegistration::UnRegistration($document, $em, $currentUser);
        
        $oldDocument = clone $document;
        
        $document->setStatusCode($command->statusCode);
        
        GoodsDecisionDocUpdate::Update($document, $oldDocument, $em, $currentUser);
                
        GoodsDecisionDocRegistration::registration($document, $em, $currentUser);
        
    }
    
}
