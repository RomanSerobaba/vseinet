<?php

namespace ReservesBundle\Bus\GoodsAcceptance\Command;

use AppBundle\Bus\Message\MessageHandler;
use ReservesBundle\Entity\GoodsAcceptance;
use ReservesBundle\Bus\GoodsAcceptance\GoodsAcceptanceRegistration;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        $currentUser = $this->get('user.identity')->getUser();

        $em = $this->getDoctrine()->getManager();
        
        // Получение номера документа

        $queryText = "select nextval('goods_acceptance_doc_number'::regclass) as id;";

        $result = $em->createNativeQuery($queryText, new ResultSetMapping())
                ->getResult('ListHydrator');
        
        $documentNumber = (int)$result[0];
        
        //
        
        $document = new GoodsAcceptance();
        
        $document->setNumber($documentNumber);
        $document->setGeoRoomId($command->geoRoomId);
        $document->setGeoRoomSource($command->geoRoomSource);
        $document->setParentDocumentId($command->parentDocumentId);
        $document->setStatusCode(GoodsAcceptance::STATUS_NEW);
        
        if (!empty($command->title)) {
            $document->setTitle($command->title);
        }else{
            if (empty($command->geoRoomSource)) {
                $document->setTitle('Поступление №'. $documentNumber);
            }else{
                $document->setTitle('Транзит №'. $documentNumber);
            }
        }
        
        $document->setCreatedAt(new \DateTime());        
        $document->setCreatedBy($currentUser->getId());
        
        $em->persist($document);
        $em->flush();
        
        $document->get('uuid.manager')->saveId($command->uuid, $item->getId());
        
        GoodsAcceptanceRegistration::registration($document, $em, $currentUser);
    }
}