<?php

namespace FinanseBundle\Bus\SupplierOrderExpensesDoc;

use FinanseBundle\Entity\SupplierOrderExpensesDoc;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;

trait SupplierOrderExpensesDocUnRegistration
{

    /**
     * Отмена регистрации документа в учетной системе.
     *
     * @param \FinanseBundle\Entity\SupplierOrderExpensesDoc  $document     регистрируемый документ
     * @param \Doctrine\ORM\EntityManager      $em           менеджер сущностей
     * @param \AppBundle\Entity\User           $currentUser  пользователь, регистрирующий документ
     */
    public function unRegistration(SupplierOrderExpensesDoc $document, EntityManager $em, User $currentUser)
    {

        if (empty($document->getRegisteredAt())) {
            return;
        }

        $this->get('register.financialExpenses')
                ->dropByRegistratorDId($document->getDId());

        $this->get('register.financialMutual')
                ->dropByRegistratorDId($document->getDId());

        $this->get('register.financialReserve')
                ->dropByRegistratorDId($document->getDId());

        $this->get('register.financialDocumentPayment')
                ->dropByRegistratorDId($document->getDId());

        // Запись шапки документа

        $document->setRegisteredAt();
        $document->setRegisteredBy();

        $em->persist($document);
        $em->flush();
    }

}
