<?php

namespace FinanseBundle\Bus\BuyerOrderExpensesDoc;

use FinanseBundle\Entity\BuyerOrderExpensesDoc;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use FinanseBundle\Entity\FinancialOperationDoc;

trait BuyerOrderExpensesDocRegistration
{

    /**
     * Регистрация документа в учетной системе.
     *
     * @param \FinanseBundle\Entity\BuyerOrderExpensesDoc  $document     регистрируемый документ
     * @param \Doctrine\ORM\EntityManager                  $em           менеджер сущностей
     * @param \AppBundle\Entity\User                       $currentUser  пользователь, регистрирующий документ
     */
    public function registration(BuyerOrderExpensesDoc $document, EntityManager $em, User $currentUser)
    {

        if (!empty($document->getRegisteredAt()))
            throw new ConflictHttpException('Документ уже проведён');

        if (empty($document->getCompletedAt()))
            return;

        ///////////////////////////////////

        if ("wait" == $document->getStatusCode()) {
            $fod = new FinancialOperationDoc();
            $fod->setCreatedBy($currentUser->getId());
            $fod->setCreatedAt(new \DateTime);
            $fod->setFinancialResourceId($document->get);
        }

        if ("completed" == $document->getStatusCode()) {
            $this->regWait($document); //регистрация выдачи наличных средств
        }

        ///////////////////////////////////
        // Отметка об удачной записи
        $document->setRegisteredAt(new \DateTime);
        $document->setRegisteredBy($currentUser->getId());

        $em->persist($document);
        $em->flush();
    }

    private function regWait($document)
    {

    }

}
