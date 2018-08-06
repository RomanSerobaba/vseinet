<?php
namespace FinanseBundle\Bus\BankOperationDoc;

use FinanseBundle\Entity\BankOperationDoc;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\ResultSetMapping;

class BankOperationDocUnRegistration
{
    /**
     * Отмена регистрации документа в учетной системе.
     * 
     * @param \FinanseBundle\Entity\BankOperationDoc  $document     регистрируемый документ
     * @param \Doctrine\ORM\EntityManager      $em           менеджер сущностей
     * @param \AppBundle\Entity\User           $currentUser  пользователь, регистрирующий документ
     */
    public static function UnRegistration(BankOperationDoc $document, EntityManager $em, User $currentUser)
    {
        
        if (empty($document->getRegisteredAt())) {
            return;
        }

        $documentDid = $document->getDId();
                
        $em->createNativeQuery(
            "delete from financial_mutual_register from registrator_did = {$documentDid};",
            new ResultSetMapping())->execute();
            
        $em->createNativeQuery(
            "delete from financial_reserve_register from registrator_did = {$documentDid};",
            new ResultSetMapping())->execute();
            
        // Запись шапки документа

        $document->setRegisteredAt();
        $document->setRegisteredBy();

        $em->persist($document);
        $em->flush();

    }
    
}
