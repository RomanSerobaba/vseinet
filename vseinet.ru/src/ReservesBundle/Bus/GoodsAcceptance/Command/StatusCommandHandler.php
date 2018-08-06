<?php

namespace ReservesBundle\Bus\GoodsAcceptance\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ReservesBundle\Entity\GoodsAcceptance;
use ReservesBundle\Bus\GoodsAcceptance\GoodsAcceptanceRegistration;
use ReservesBundle\Bus\GoodsAcceptance\GoodsAcceptanceUnRegistration;
use ReservesBundle\Bus\GoodsAcceptance\GoodsAcceptanceUpdate;

class StatusCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(StatusCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(GoodsAcceptance::class)->find($command->id);
        if (!$document instanceof GoodsAcceptance) {
            throw new NotFoundHttpException('Документ не найден.');
        }

        if ($document->getStatusCode() == $command->statusCode) return; // Проверка наличия изменений
        
        $currentUser = $this->get('user.identity')->getUser();
        
        GoodsAcceptanceUnRegistration::UnRegistration($document, $em, $currentUser);
        
        $oldDocument = clone $document;
        
        $document->setStatusCode($command->statusCode);
        
        GoodsAcceptanceUpdate::Update($document, $oldDocument, $em, $currentUser);
                
        GoodsAcceptanceRegistration::registration($document, $em, $currentUser);
        
    }
    
}
