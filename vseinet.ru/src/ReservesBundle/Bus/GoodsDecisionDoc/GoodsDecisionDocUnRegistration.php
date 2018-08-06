<?php

namespace ReservesBundle\Bus\GoodsDecisionDoc;

use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\Entity\GoodsDecisionDoc;

class GoodsDecisionDocUnRegistration
{
    /**
     * Отмена регистрации документа в учетной системе.
     * 
     * @param \ReservesBundle\Entity\GoodsDecisionDoc $document    регистрируемый документ
     * @param \Doctrine\ORM\entityManager             $em          менеджер сущностей
     * @param \AppBundle\Entity\User                  $currentUser пользователь, регистрирующий документ
     */
    public static function UnRegistration(GoodsDecisionDoc $document, \Doctrine\ORM\entityManager $em, \AppBundle\Entity\User $currentUser)
    {
        
        if (empty($document->getRegisteredAt())) {
            return;
        }

        // Отмена проведения документа
        
        // Удаляем старые записи из регистра претензий по товару

        $queryText = "
            delete from goods_issue_register
            where 
                registrator_did = :registratorId
            ";
        
        $queryResultMapping = new ResultSetMapping();
        $queryResultMapping->addScalarResult('id', 'id', 'integer');

        $em->createNativeQuery($queryText, $queryResultMapping)
                ->setParameters(['registratorId' => $document->getDId()])
                ->execute();
        
        // Удаляем старые записи из движений товаров

        $rsm = new ResultSetMapping();

        $queryText = "
            delete from goods_reserve_register
            where
                registrator_type_code = '". \AppBundle\Enum\DocumentTypeCode::GOODS_DECISION ."'::document_type_code and
                registrator_id = :registratorId
        ";

        $em->createNativeQuery($queryText, $rsm)
                ->setParameters(['registratorId' => $document->getDId()])
                ->execute();

        // Запись шапки документа

        $document->setRegisteredAt();
        $document->setRegisteredBy();

        $em->persist($document);
        $em->flush();

    }
    
}
