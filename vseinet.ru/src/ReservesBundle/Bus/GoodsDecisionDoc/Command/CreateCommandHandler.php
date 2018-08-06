<?php

namespace ReservesBundle\Bus\GoodsDecisionDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ReservesBundle\Entity\GoodsDecisionDoc;
use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\Bus\GoodsDecisionDoc\GoodsDecisionDocRegistration;
class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        $currentUser = $this->get('user.identity')->getUser();

        $em = $this->getDoctrine()->getManager();

        // Получение номера документа

        $queryText = "select nextval('goods_decision_doc_number_seq'::regclass) as id;";

        $result = $em->createNativeQuery($queryText, new ResultSetMapping())
                ->getResult('ListHydrator');
        
        $documentNumber = (int)$result[0];
        
        //
        
        $document = new GoodsDecisionDoc();
        
        $document->setParentDocumentId($command->parentDocumentId);

        $document->setCreatedBy($currentUser->getId());
        
        if (!empty($command->title)) {
            $document->setTitle($command->title);
        }else{
            $document->setTitle("Решение №". $documentNumber);
        }
        
        $document->setNumber($documentNumber);
        $document->setDescription($command->description);
        $document->setQuantity($command->quantity);
        $document->setGeoRoomId($command->geoRoomId);
        $document->setBaseProductId($command->baseProductId);
        $document->setPrice($command->price);
        $document->setMoneyBack($command->moneyBack);
        $document->setGoodsIssueDocumentId($command->goodsIssueDocumentId);
        $document->setGoodsDecisionDocTypeId($command->goodsDecisionDocTypeId);
        $document->setStatusCode($command->statusCode);
        
        $em->persist($document);
        
        $em->flush();
        
        $this->get('uuid.manager')->saveId($command->uuid, $document->getDId());
        
        GoodsDecisionDocRegistration::Registration($document, $em, $currentUser);

    }
    
}
