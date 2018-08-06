<?php

namespace FinanseBundle\Bus\AccountableExpensesDoc;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use FinanseBundle\Entity\AccountableExpensesDoc;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

trait AccountableExpensesDocUpdate
{

    /**
     * Обновление документа
     *
     * @param \FinanseBundle\Entity\AccountableExpensesDoc  $newDocument  регистрируемый документ
     * @param \FinanseBundle\Entity\AccountableExpensesDoc  $oldDocument  старый регистрируемый документ
     * @param \Doctrine\ORM\EntityManager      $em           менеджер сущностей
     * @param \AppBundle\Entity\User           $currentUser  пользователь, регистрирующий документ
     */
    public function update(AccountableExpensesDoc $newDocument, AccountableExpensesDoc $oldDocument, \Doctrine\ORM\entityManager $em, \AppBundle\Entity\User $currentUser)
    {

        // Общие проверки документа на корректность заполнения

        if (!empty($oldDocument->getCompletedAt()))
            throw new ConflictHttpException('Изменение завершенного документа невозможно');

        if (!empty($oldDocument->getRegisteredAt()))
            throw new ConflictHttpException('Изменение зарегистрированного документа невозможно');

        if ($newDocument->getStatusCode() != $oldDocument->getStatusCode()) {

            $newStatus = $this->get('document.status')
                    ->checkNewStatus(AccountableExpensesDoc::class, $oldDocument->getStatusCode(), $newDocument->getStatusCode());

            if ($newStatus['completing']) {

                $newDocument->setCompletedAt(new \DateTime);
                $newDocument->setCompletedBy($currentUser->getId());
            } else {

                $newDocument->setCompletedAt();
                $newDocument->setCompletedBy();
            }
        }

        // Особые проверки документа на корректность заполнения

        switch ($newDocument->getStatusCode()) {

            case 'new':
                break;

            case 'active':
                $newDocument->setAcceptedBy($currentUser->getId());
                $newDocument->setAcceptedAt(new \DateTime);
                break;

            case 'rejected':
                $newDocument->setRejectedBy($currentUser->getId());
                $newDocument->setRejectedAt(new \DateTime);
                break;

            case 'wait':
                $newDocument->setPaymentBy($currentUser->getId());
                $newDocument->setPaymentAt(new \DateTime);
                $newDocument->setMaturityDatePayment($newDocument->getPaymentAt()->add(new \DateInterval('P1M')));
                break;

            case 'completed':
                break;

            default:
                throw new BadRequestHttpException('Неизвестный статус документа');
                break;
        }

        //////////////////////////////////////////////////////

        $em->persist($newDocument);
        $em->flush();
    }

}
