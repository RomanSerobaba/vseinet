<?php
namespace FinanseBundle\Bus\FinancialOperationDoc;

use FinanseBundle\Entity\FinancialOperationDoc;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class FinancialOperationDocRegistration
{

    /**
     * Регистрация документа в учетной системе.
     *
     * @param \FinanseBundle\Entity\FinancialOperationDoc  $document     регистрируемый документ
     * @param \Doctrine\ORM\EntityManager      $em           менеджер сущностей
     * @param \AppBundle\Entity\User           $currentUser  пользователь, регистрирующий документ
     */
    public static function Registration(FinancialOperationDoc $document, EntityManager $em, User $currentUser)
    {

        if (!empty($document->getRegisteredAt()))
            throw new ConflictHttpException('Документ уже проведён');

        if (empty($document->getCompletedAt()))
            return;

        // Отметка об удачной записи
        $document->setRegisteredAt(new \DateTime);
        $document->setRegisteredBy($currentUser->getId());

        $em->persist($document);
        $em->flush();

    }

}
