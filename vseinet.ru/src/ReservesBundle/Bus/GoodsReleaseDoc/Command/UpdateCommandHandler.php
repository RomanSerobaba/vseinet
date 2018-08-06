<?php

namespace ReservesBundle\Bus\GoodsReleaseDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Enum\OperationTypeCode;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\Entity\GoodsReleaseDoc;
use ReservesBundle\Bus\GoodsReleaseDoc\GoodsReleaseDocRegistration;
use ReservesBundle\Bus\GoodsReleaseDoc\GoodsReleaseDocUnRegistration;
use ReservesBundle\Bus\GoodsReleaseDoc\GoodsReleaseDocUpdate;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command) 
    {

        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(GoodsReleaseDoc::class)->find($command->id);
        if (!$document instanceof GoodsReleaseDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }
        
        $currentUser = $this->get('user.identity')->getUser();
        
        GoodsReleaseDocUnRegistration::UnRegistration($document, $em, $currentUser);
        
        $oldDocument = clone $document;
        
        if (    // Проверка наличия изменений
                $document->getStatusCode() == $command->statusCode and
                $document->getGeoRoomId() == $command->geoRoomId and
                $document->getDestinationRoomId() == $command->destinationRoomId
                
                ) return;
        
        $document->setStatusCode($command->statusCode);
        $document->setGeoRoomId($command->geoRoomId);
        $document->setDestinationRoomId($command->destinationRoomId);
        
        GoodsReleaseDocUpdate::Update($document, $oldDocument, $em, $currentUser);
                
        GoodsReleaseDocRegistration::registration($document, $em, $currentUser);
        
    }
    
}
