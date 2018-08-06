<?php

namespace ReservesBundle\Bus\GoodsReleaseDoc;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\Entity\GoodsReleaseDoc;

class GoodsReleaseDocUnRegistration
{
    /**
     * Отмена регистрации документа в учетной системе.
     * 
     * @param \ReservesBundle\Entity\GoodsReleaseDoc $document    регистрируемый документ
     * @param \Doctrine\ORM\entityManager            $em          менеджер сущностей
     * @param \AppBundle\Entity\User                 $currentUser пользователь, регистрирующий документ
     */
    public static function UnRegistration(GoodsReleaseDoc $document, \Doctrine\ORM\entityManager $em, \AppBundle\Entity\User $currentUser)
    {
        
        if (empty($document->getRegisteredAt())) {
            return;
        }
        
        // Удаляем дочерние документы

        $rsm = new ResultSetMapping();

        $queryText = "
            delete from any_doc*
            where
                document_parent_did = :parentDocumentId
        ";

        $queryDB = $em->createNativeQuery($queryText, $rsm)
                ->setParameters(['parentDocumentId' => $document->getDId()]);

        $queryDB->execute();

        // Удаляем старые записи из движений товаров

        $rsm = new ResultSetMapping();

        $queryText = "
            delete from goods_reserve_register
            where
                registrator_type_code = '". \AppBundle\Enum\DocumentTypeCode::GOODS_RELEASE ."'::operation_type_code and
                registrator_id = :goodsReleaseDocDId
        ";

        $queryDB = $em->createNativeQuery($queryText, $rsm)
                ->setParameters(['goodsReleaseDocDId' => $document->getDId()]);

        $queryDB->execute();

        // Запись шапки документа
        
        $document->setRegistredAt();
        $document->setRegistredBy();

        $em->persist($document);
        $em->flush();

    }
    
}
