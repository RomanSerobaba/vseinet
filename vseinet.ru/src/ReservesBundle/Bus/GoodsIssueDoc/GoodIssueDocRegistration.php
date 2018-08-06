<?php

namespace ReservesBundle\Bus\GoodsIssueDoc;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use RegisterBundle\Entity\GoodsReserveRegister;
use ReservesBundle\Entity\GoodsIssueRegister;
use ReservesBundle\Entity\GoodsIssueDoc;
use ReservesBundle\Entity\GoodsIssueDocType;

class GoodIssueDocRegistration
{

    /*
     * Регистрация документа в учетной системе.
     *
     * @param \ReservesBundle\Entity\GoodsIssueDoc $document    регистрируемый документ
     * @param \Doctrine\ORM\entityManager          $em          менеджер сущностей
     * @param \AppBundle\Entity\User               $currentUser пользователь, регистрирующий документ
     */
    static function registration(GoodsIssueDoc $document, \Doctrine\ORM\entityManager $em, \AppBundle\Entity\User $currentUser)
    {

        if (!empty($document->getRegisteredAt())) {
            throw new ConflictHttpException('Документ уже проведён');
        }

        // Пометка товара как проблемного

        if (in_array($document->getStatusCode(), ['new', 'active', 'completed'])) {

            $goodsIssueDocType = $em->getRepository(GoodsIssueDocType::class)->find($document->getGoodsIssueDocTypeId());
            if (!$goodsIssueDocType instanceof GoodsIssueDocType) {
                throw new NotFoundHttpException('Тип документа некорректен.');
            }
            
            if ($goodsIssueDocType->getMakeIssueReserve()) {

                $goodsIssueDocType = new GoodsIssueDocType();

                // сторнирование товара
                $grr = new GoodsReserveRegister();

                $grr->setCreatedAt(new \DateTime);
                $grr->setCreatedBy($currentUser->getId());
                $grr->setRegistratorId($document->getDId());
                $grr->setRegistratorTypeCode(\AppBundle\Enum\DocumentTypeCode::GOODS_ISSUE);
                $grr->setRegisteredAt($document->getCreatedAt());
                $grr->setRegisterOperationTypeCode(\AppBundle\Enum\OperationTypeCode::GOODS_ISSUE_CREATION);

                $grr->setBaseProductId($document->getBaseProductId());
                $grr->setSupplyItemId($document->getSupplyItemId());
                $grr->setOrderItemId($document->getOrderItemId());
                $grr->setGeoRoomId($document->getGeoRoomId());
                if (empty($document->getOrderItemId())) {
                    $grr->setGoodsConditionCode('free');
                }else{
                    $grr->setGoodsConditionCode('reserved');
                }
                $grr->setDelta(-$document->getQuantity());

                
                $em->persist($grr);

                // опризодование проблемного товара
                $grr = new GoodsReserveRegister();

                $grr->setCreatedAt(new \DateTime);
                $grr->setCreatedBy($currentUser->getId());
                $grr->setRegistratorId($document->getDId());
                $grr->setRegistratorTypeCode(\AppBundle\Enum\DocumentTypeCode::GOODS_ISSUE);
                $grr->setRegisteredAt($document->getCreatedAt());
                $grr->setRegisterOperationTypeCode(\AppBundle\Enum\OperationTypeCode::GOODS_ISSUE_CREATION);

                $grr->setBaseProductId($document->getBaseProductId());
                $grr->setSupplyItemId($document->getSupplyItemId());
                $grr->setOrderItemId(null);
                $grr->setGeoRoomId($document->getGeoRoomId());
                $grr->setGoodsConditionCode('issued');
                $grr->setDelta($document->getQuantity());

                $em->persist($grr);
                
            }
            
        }

        // Запись о принятии претензии в работу

        if (in_array($document->getStatusCode(), ['active', 'completed'])) {

            $newRegisteredAt = new \DateTime;

            $documentType = $em->getRepository(GoodsIssueDocType::class)->find($document->getGoodsIssueDocTypeId());

            $gir = new GoodsIssueRegister();

            // Запись регистратора
            $gir->setCreatedBy($currentUser->getId());
            $gir->setRegistratorDId($document->getDId());
            $gir->setRegisteredAt($document->getActivatedAt());

            // Запись измерений
            $gir->setGoodsIssueDocDId($document->getDId());

            if ($documentType->getByGoods()) {
                $gir->setDeltaGoods($document->getQuantity());
            }

            if ($documentType->getByClient()) {
                $gir->setDeltaClient($document->getQuantity());
            }

            if ($documentType->getBySupplier()) {
                $gir->setDeltaSupplye($document->getQuantity());
            }

            $em->persist($gir);
            
        }

        if (in_array($document->getStatusCode(), ['completed'])) {
            
        }

        // Отметка об удачной записи
        $document->setRegisteredAt(new \DateTime);
        $document->setRegisteredBy($currentUser->getId());

        $em->persist($document);
        $em->flush();

    }

}
