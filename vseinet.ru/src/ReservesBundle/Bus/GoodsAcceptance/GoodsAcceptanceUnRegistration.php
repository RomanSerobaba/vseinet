<?php

namespace ReservesBundle\Bus\GoodsAcceptance;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\Entity\GoodsAcceptance;

class GoodsAcceptanceUnRegistration
{
    /**
     * Отмена регистрации документа в учетной системе.
     * 
     * @param \ReservesBundle\Entity\GoodsAcceptance $document    регистрируемый документ
     * @param \Doctrine\ORM\entityManager            $em          менеджер сущностей
     * @param \AppBundle\Entity\User                 $currentUser пользователь, регистрирующий документ
     */
    public static function UnRegistration(GoodsAcceptance $document, \Doctrine\ORM\entityManager $em, \AppBundle\Entity\User $currentUser)
    {
        
        if (empty($document->getRegisteredAt())) {
            return;
        }
        
        // Удаляем дочерние документы

        $rsm = new ResultSetMapping();

        $queryText = "
            delete from any_doc*
            where
                parent_doc_did = :parentDocumentId
        ";

        $queryDB = $em->createNativeQuery($queryText, $rsm)
                ->setParameters(['parentDocumentId' => $document->getDId()]);

        $queryDB->execute();

        // Удаляем старые записи из движений товаров

        $rsm = new ResultSetMapping();

        $queryText = "
            delete from goods_reserve_register
            where
                registrator_type_code = '". \AppBundle\Enum\DocumentTypeCode::GOODS_ACCEPTANCE ."'::document_type_code and
                registrator_id = :goodsReleaseDocDId
        ";

        $queryDB = $em->createNativeQuery($queryText, $rsm)
                ->setParameters(['goodsReleaseDocDId' => $document->getDId()]);

        $queryDB->execute();

        // Запись шапки документа
        
        $document->setRegisteredAt();
        $document->setRegisteredBy();

        $em->persist($document);
        $em->flush();

    }
    
}
