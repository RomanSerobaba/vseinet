<?php

namespace FinanseBundle\Bus\BuyerOrderExpensesDoc;

use FinanseBundle\Entity\BuyerOrderExpensesDoc;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;

trait BuyerOrderExpensesDocUnRegistration
{

    /**
     * Отмена регистрации документа в учетной системе.
     *
     * @param \FinanseBundle\Entity\BuyerOrderExpensesDoc  $document     регистрируемый документ
     * @param \Doctrine\ORM\EntityManager      $em           менеджер сущностей
     * @param \AppBundle\Entity\User           $currentUser  пользователь, регистрирующий документ
     */
    public function unRegistration(BuyerOrderExpensesDoc $document, EntityManager $em, User $currentUser)
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
