<?php

namespace ReservesBundle\Bus\GoodsIssueDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\Entity\GoodsIssueDoc;
use ReservesBundle\Entity\GoodsIssueDocProduct;

use ReservesBundle\Bus\GoodsIssueDoc\GoodIssueDocRegistration;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {

        $em = $this->getDoctrine()->getManager();

        $currentUser = $this->get('user.identity')->getUser();
        
        // Получение номера документа

        $queryText = "select nextval('goods_issue_doc_id_seq'::regclass) as id;";

        $result = $em->createNativeQuery($queryText, new ResultSetMapping())
                ->getResult('ListHydrator');
        
        $documentNumber = (int)$result[0];
        
        //
        
        $document = new GoodsIssueDoc();
        
        $document->setParentDocumentId($command->parentDocumentId);

        $document->setCreatedBy($currentUser->getId());
        
        if (!empty($command->title)) {
            $document->setTitle($command->title);
        }else{
            $document->setTitle("Претензия №". $documentNumber);
        }
        
        $document->setNumber($documentNumber);
        $document->setGoodsCondition($command->goodsCondition);
        $document->setDescription($command->description);
        $document->setGeoRoomId($command->geoRoomId);
        $document->setSupplierId($command->supplierId);
        $document->setGoodsIssueDocTypeId($command->goodsIssueDocTypeId);
        $document->setStatusCode(GoodsIssueDoc::STATUS_NEW);
        $document->setBaseProductId($command->baseProductId);
        $document->setOrderItemId($command->orderItemId);
        $document->setSupplyItemId($command->supplyItemId);
        $document->setGoodsStateCode($command->goodsStateCode);
        $document->setQuantity($command->quantity);

        $em->persist($document);
        $em->flush($document);
        
        GoodIssueDocRegistration::registration($document, $em, $currentUser);
        
        $this->get('uuid.manager')->saveId($command->uuid, $document->getDId());

    }
    
}
