<?php

namespace ReservesBundle\Bus\GoodsIssueDoc;

use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\Entity\GoodsIssueDoc;

class GoodIssueDocUnRegistration
{

    /**
     * Отмена регистрации документа в учетной системе.
     * 
     * @param \ReservesBundle\Entity\GoodsIssueDoc $document    регистрируемый документ
     * @param \Doctrine\ORM\entityManager          $em          менеджер сущностей
     * @param \AppBundle\Entity\User               $currentUser пользователь, регистрирующий документ
     */
    static function unRegistration(GoodsIssueDoc $document, \Doctrine\ORM\entityManager $em, \AppBundle\Entity\User $currentUser)
    {
        
        if (empty($document->getRegisteredAt())) {
            return;
        }

        // Отмена проведения документа
        
        // Регистр претензий

        $queryText = "
            delete
            from goods_issue_register
            where 
            registrator_did = :registratorDId
            ";
        
        $queryResultMapping = new ResultSetMapping();
        $queryResultMapping->addScalarResult('id', 'id', 'integer');

        $em->createNativeQuery($queryText, $queryResultMapping)
                ->setParameters([
                    'registratorDId' => $document->getDId(),
                ])
                ->execute();
        
        // Регистр остатков

        $queryText = "
            delete
            from goods_reserve_register
            where 
            registrator_id = :registratorDId and
            registrator_type_code = 'goods_issue'::document_type_code
            ";
        
        $em->createNativeQuery($queryText, new ResultSetMapping())
                ->setParameters([
                    'registratorDId' => $document->getDId(),
                ])
                ->execute();
        
        // Запись шапки документа

        $document->setRegisteredAt();
        $document->setRegisteredBy();

        $em->persist($document);
        $em->flush();
        
    }
    
}
