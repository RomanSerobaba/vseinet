<?php
/*
 * Автор: Денис О. Конашёнок
 */

namespace ReservesBundle\Bus\GoodsDecisionDoc;

use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
//use Doctrine\ORM\Query\ResultSetMapping;
//use AppBundle\Enum\OperationTypeCode;
//use AppBundle\Enum\GoodsAcceptanceType;
use ReservesBundle\Entity\GoodsDecisionDoc;
use RegisterBundle\Entity\GoodsReserveRegister;
use ReservesBundle\Entity\GoodsIssueRegister;
//use SupplyBundle\Entity\SupplyItem;

class GoodsDecisionDocRegistration
{

    /**
     * Регистрация документа в учетной системе.
     *
     * @param \ReservesBundle\Entity\GoodsAcceptance $document    регистрируемый документ
     * @param \Doctrine\ORM\entityManager            $em          менеджер сущностей
     * @param \AppBundle\Entity\User                 $currentUser пользователь, регистрирующий документ
     */
    public static function Registration(GoodsDecisionDoc $document, \Doctrine\ORM\entityManager $em, \AppBundle\Entity\User $currentUser)
    {

        if (!empty($document->getRegisteredAt()))
            throw new ConflictHttpException('Документ уже проведён');

        if (empty($document->getCompletedAt()))
            return;

//        $currentUser = $this->get('user.identity')->getUser();
//        
//        $em = $this->getDoctrine()->getManager();
//        
//        $document = $em->getRepository(GoodsDecisionDoc::class)->find($command->id);
//        if (!$document instanceof GoodsDecisionDoc) {
//            throw new NotFoundHttpException('Документ не найден (команда)');
//        }
//        
//        if (!empty($document->getRegisteredAt())) {
//            throw new ConflictHttpException('Документ уже проведён (команда)');
//        }
//
//        // Проверка готовности корректно заполненного документа к проведению
//        
//        // Записываем новые движения документа
//
//        $documentType = $em->getRepository(GoodsDecisionDocType::class)->find($document->getGoodsDecisionDocTypeId());
//        if (!$documentType instanceof GoodsDecisionDocType) {
//            throw new NotFoundHttpException('Неизвестный тип претензии.');
//        }
//        
//        $gir = new GoodsIssueRegister();
//        
//        $gir->setCreatedBy($currentUser->getId());
//        $gir->setGoodsIssueDocId($document->getGoodsIssueDocId());
//        $gir->setRegisteredAt($document->getCompletedAt());
//        $gir->setRegistratorId($document->getId());
//        $gir->setRegistratorTypeCode(\AppBundle\Enum\DocumentTypeCode::GOODS_DECISION);
//        
//        if ($documentType->getByGoods()) {
//            $gir->setDeltaGoods(-$document->getQuantity());
//        }
//        
//        if ($documentType->getByClient()) {
//            $gir->setDeltaClient(-$document->getQuantity());
//        }
//        
//        if ($documentType->getBySupplier()) {
//            $gir->setDeltaSupplye(-$document->getQuantity());
//        }
//        
//        $em->persist($gir);
//                
//        // Отметка об удачной записи
//        $document->setRegisteredAt(new \DateTime);
//        $document->setRegisteredBy($currentUser->getId());
//
//        $em->persist($document);
//        $em->flush();
        
    }

}
