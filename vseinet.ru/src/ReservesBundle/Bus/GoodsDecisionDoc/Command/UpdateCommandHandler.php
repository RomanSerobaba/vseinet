<?php

namespace ReservesBundle\Bus\GoodsDecisionDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\Entity\GoodsDecisionDoc;
use ReservesBundle\Bus\GoodsDecisionDoc\GoodsDecisionDocUnRegistration;
use ReservesBundle\Bus\GoodsDecisionDoc\GoodsDecisionDocUpdate;
use ReservesBundle\Bus\GoodsDecisionDoc\GoodsDecisionDocRegistration;

class UpdateCommandHandler extends MessageHandler
{
    
    public function handle(UpdateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(GoodsDecisionDoc::class)->find($command->id);
        if (!$document instanceof GoodsDecisionDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }
        
        $currentUser = $this->get('user.identity')->getUser();
        
        GoodsDecisionDocUnRegistration::UnRegistration($document, $em, $currentUser);
        
        $oldDocument = clone $document;
        $document = new GoodsDecisionDoc();
        if (    // Проверка наличия изменений
                $document->getTitle() == $command->title and
                $document->getStatusCode() == $command->statusCode and
                $document->getGeoRoomId() == $command->geoRoomId and
                $document->getDescription() == $command->description and
                $document->getQuantity() == $command->quantity and
                $document->getBaseProductId() == $command->baseProductId and
                $document->getMoneyBack() == $command->moneyBack and
                $document->getPrice() == $command->price
                ) return;

        $document->setTitle($command->title);
        $document->setStatusCode($command->statusCode);
        $document->setGeoRoomId($command->geoRoomId);
        $document->setDescription($command->description);
        $document->setQuantity($command->quantity);
        $document->setBaseProductId($command->baseProductId);
        $document->setMoneyBack($command->moneyBack);
        $document->setPrice($command->price);
        
        GoodsDecisionDocUpdate::Update($document, $oldDocument, $em, $currentUser);
                
        GoodsDecisionDocRegistration::Registration($document, $em, $currentUser);
        
    }
    
}
