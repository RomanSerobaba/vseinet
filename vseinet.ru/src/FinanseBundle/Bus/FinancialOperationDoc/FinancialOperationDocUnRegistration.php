<?php
namespace FinanseBundle\Bus\FinancialOperationDoc;

use FinanseBundle\Entity\FinancialOperationDoc;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;

class FinancialOperationDocUnRegistration
{
    /**
     * Отмена регистрации документа в учетной системе.
     * 
     * @param \FinanseBundle\Entity\FinancialOperationDoc  $document     регистрируемый документ
     * @param \Doctrine\ORM\EntityManager      $em           менеджер сущностей
     * @param \AppBundle\Entity\User           $currentUser  пользователь, регистрирующий документ
     */
    public static function UnRegistration(FinancialOperationDoc $document, EntityManager $em, User $currentUser)
    {
        
        if (empty($document->getRegisteredAt())) {
            return;
        }

        // Запись шапки документа

        $document->setRegisteredAt();
        $document->setRegisteredBy();

        $em->persist($document);
        $em->flush();

    }
    
}
