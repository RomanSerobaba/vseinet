<?php

namespace ReservesBundle\Bus\GoodsReleaseDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ReservesBundle\Entity\GoodsReleaseDoc;
use ReservesBundle\Bus\GoodsReleaseDoc\GoodsReleaseDocRegistration;
use ReservesBundle\Bus\GoodsReleaseDoc\GoodsReleaseDocUnRegistration;
use ReservesBundle\Bus\GoodsReleaseDoc\GoodsReleaseDocUpdate;

class StatusCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(StatusCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(GoodsReleaseDoc::class)->find($command->id);
        if (!$document instanceof GoodsReleaseDoc) {
            throw new NotFoundHttpException('Документ не найден.');
        }

        if ($document->getStatusCode() == $command->statusCode) return; // Проверка наличия изменений
        
        $currentUser = $this->get('user.identity')->getUser();
        
        GoodsReleaseDocUnRegistration::UnRegistration($document, $em, $currentUser);
        
        $oldDocument = clone $document;
        
        $document->setStatusCode($command->statusCode);
        
        GoodsReleaseDocUpdate::Update($document, $oldDocument, $em, $currentUser);
                
        GoodsReleaseDocRegistration::registration($document, $em, $currentUser);
        
    }
    
}
