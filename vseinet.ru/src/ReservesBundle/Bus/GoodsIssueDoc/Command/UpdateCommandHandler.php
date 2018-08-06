<?php

namespace ReservesBundle\Bus\GoodsIssueDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\Entity\GoodsIssueDoc;
use ReservesBundle\Entity\GoodsIssueDocType;
use ReservesBundle\Entity\GoodsIssueDocProduct;
use ReservesBundle\Bus\GoodsIssueDoc\GoodIssueDocRegistration;
use ReservesBundle\Bus\GoodsIssueDoc\GoodIssueDocUnRegistration;

class UpdateCommandHandler extends MessageHandler
{

    public function handle(UpdateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $document = $em->getRepository(GoodsIssueDoc::class)->find($command->id);
        if (!$document instanceof GoodsIssueDoc) {
            throw new NotFoundHttpException('Документ не найден.');
        }
        
        if (!empty($document->getCompletedAt())) {
            throw new ConflictHttpException('Изменение завершенного документа невозможно.');
        }

        $documentType = $em->getRepository(GoodsIssueDocType::class)->find($document->getGoodsIssueDocTypeId());
        if (!$documentType instanceof GoodsIssueDocType) {
            throw new ConflictHttpException('Неизвестный тип претензии.');
        }
        
        $currentUser = $this->get('user.identity')->getUser();
        
        GoodIssueDocUnRegistration::unRegistration($document, $em, $currentUser);
        
        // Проверка корректности нового статуса документа

        if ($document->getStatusCode() != $command->statusCode) {
            
            // Измененение статуса докмуента
            
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult("name", "name", "string");
            $rsm->addScalarResult("status_code", "statusCode", "string");
            $rsm->addScalarResult("active", "active", "boolean");
            $rsm->addScalarResult("completing", "completing", "boolean");
            $rsm->addScalarResult("available_new_status_code", "availableNewStatusCode", "string");

            $newStatus = $em->createNativeQuery("
                select
                    name,
                    status_code,
                    active,
                    completing,
                    available_new_status_code
                from goods_issue_doc_status
                where 
                    status_code = '{$command->statusCode}'::any_doc_status_code
                limit 1
                ", $rsm)->getResult();

            if (empty($newStatus)) {
                throw new BadRequestHttpException('Статус не соответствует документу');
            }
            $newStatus = $newStatus[0];

            if (!$newStatus['active']) {
                throw new ConflictHttpException('Новый статус документа недоступен для использования');
            }

            $curStatus = $em->createNativeQuery("
                select
                    name,
                    status_code,
                    active,
                    completing,
                    available_new_status_code
                from goods_issue_doc_status
                where 
                    status_code = '{$document->getStatusCode()}'::any_doc_status_code
                limit 1
                ", $rsm)->getResult();

            if (empty($curStatus)) {
                throw new ConflictHttpException('Неожиданный текущий статус документа');
            }
            $curStatus = $curStatus[0];

            // проверка возможности перехода на новый статус
            if (!preg_match("/\W". $newStatus['statusCode'] ."\W/", $curStatus['availableNewStatusCode'])) {
                throw new ConflictHttpException('Неожиданный новый статус документа');
            }

        }
        
        // Проверка готовности корректно заполненного документа к проведению
        
        $document->setTitle($command->title);
        $document->setNumber($command->number);
        $document->setGoodsCondition($command->goodsCondition);
        $document->setDescription($command->description);
        $document->setGeoRoomId($command->geoRoomId);
        $document->setSupplierId($command->supplierId);
        $document->setGoodsIssueDocTypeId($command->goodsIssueDocTypeId);
        $document->setStatusCode($command->statusCode);
        $document->setActivatedAt(
                (GoodsIssueDoc::STATUS_ACTIVE == $command->statusCode && empty($document->getActivatedAt()))
                ? new \DateTime
                : $document->getActivatedAt());
        $document->setBaseProductId($command->baseProductId);
        $document->setGoodsStateCode($command->goodsStateCode);
        $document->setOrderItemId($command->orderItemId);
        $document->setSupplyItemId($command->supplyItemId);
        $document->setQuantity($command->quantity);
            
        $em->persist($document);
        $em->flush();
        
        GoodIssueDocRegistration::registration($document, $em, $currentUser);
        
    }
    
}
