<?php

namespace ReservesBundle\Bus\GoodsReleaseDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ReservesBundle\Entity\GoodsReleaseDoc;
use ReservesBundle\Bus\GoodsReleaseDoc\GoodsReleaseDocRegistration;
use AppBundle\Enum\GoodsReleaseType;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        
        // Проверка корерктности входных данных
        
        if (GoodsReleaseType::MOVEMENT == $command->goodsReleaseType && empty($command->destinationRoomId))
            throw new BadRequestHttpException('При типе заказа "внутреннее перемещение" обязательно должен быть указан склад-приёмник.');
            
        if (GoodsReleaseType::TRANSIT == $command->goodsReleaseType && empty($command->destinationRoomId))
            throw new BadRequestHttpException('При типе заказа "перемещение" обязательно должен быть указан склад-приёмник.');

        //

        $em = $this->getDoctrine()->getManager();
        
        // Получение номера документа
        
        $queryText = "select nextval('goods_release_number_seq'::regclass) as id;";
        $result = $em->createNativeQuery($queryText, new ResultSetMapping())->getResult('ListHydrator');
        $documentNumber = (int)$result[0];
        
        //
        
        $currentUser = $this->get('user.identity')->getUser();
        
        $document = new GoodsReleaseDoc();
        
        $document->setNumber($documentNumber);
        $document->setGeoRoomId($command->geoRoomId);
        $document->setStatusCode(GoodsReleaseDoc::STATUS_NEW);
        $document->setDestinationRoomId($command->destinationRoomId);
        $document->setParentDocumentId($command->parentDocumentId);
        $document->setGoodsReleaseType($command->goodsReleaseType);
        $document->setIsWaiting(true == $command->isWaiting ? true : false);
        
        $document->setCreatedAt(new \DateTime());        
        $document->setCreatedBy($currentUser->getId());
        
        if (empty($command->title)) {
            $command->title = 'Отгрузка товара №'. $documentNumber;
        }
        
        $document->setTitle($command->title);
        
        $em->persist($document);
        $em->flush();

        GoodsReleaseDocRegistration::Registration($document, $em, $currentUser);
        
        $this->get('uuid.manager')->saveId($command->uuid, $document->getDId());
    }
}