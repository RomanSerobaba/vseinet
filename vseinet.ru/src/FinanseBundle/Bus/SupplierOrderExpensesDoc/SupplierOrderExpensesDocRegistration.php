<?php

namespace FinanseBundle\Bus\SupplierOrderExpensesDoc;

use FinanseBundle\Entity\SupplierOrderExpensesDoc;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

trait SupplierOrderExpensesDocRegistration
{

    /**
     * Регистрация документа в учетной системе.
     *
     * @param \FinanseBundle\Entity\SupplierOrderExpensesDoc  $document     регистрируемый документ
     * @param \Doctrine\ORM\EntityManager      $em           менеджер сущностей
     * @param \AppBundle\Entity\User           $currentUser  пользователь, регистрирующий документ
     */
    public function registration(SupplierOrderExpensesDoc $document, EntityManager $em, User $currentUser)
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
