<?php

namespace ReservesBundle\Bus\GoodsAcceptance\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ReservesBundle\Entity\GoodsAcceptance;
use ReservesBundle\Bus\GoodsAcceptance\GoodsAcceptanceUnRegistration;
use ReservesBundle\Bus\GoodsAcceptance\GoodsAcceptanceUpdate;
use ReservesBundle\Bus\GoodsAcceptance\GoodsAcceptanceRegistration;

class UpdateCommandHandler extends MessageHandler
{
    
    public function handle(UpdateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(GoodsAcceptance::class)->find($command->id);
        if (!$document instanceof GoodsAcceptance) {
            throw new NotFoundHttpException('Документ не найден');
        }
        
        $currentUser = $this->get('user.identity')->getUser();
        
        GoodsAcceptanceUnRegistration::UnRegistration($document, $em, $currentUser);
        
        $oldDocument = clone $document;
        
        if (    // Проверка наличия изменений
                $document->getTitle() == $command->title and
                $document->getStatusCode() == $command->statusCode and
                $document->getGeoRoomId() == $command->geoRoomId and
                $document->getGeoRoomSource() == $command->geoRoomSource
                
                ) return;
        
        $document->setTitle($command->title);
        $document->setStatusCode($command->statusCode);
        $document->setGeoRoomId($command->geoRoomId);
        $document->setGeoRoomSource($command->geoRoomSource);
        
        GoodsAcceptanceUpdate::Update($document, $oldDocument, $em, $currentUser);
                
        GoodsAcceptanceRegistration::registration($document, $em, $currentUser);
        
    }
    
}
