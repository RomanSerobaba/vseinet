<?php

namespace FinanseBundle\Bus\ExpenseSimpleDoc;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use FinanseBundle\Entity\ExpenseSimpleDoc;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

trait ExpenseSimpleDocUpdate
{

    /**
     * Обновление документа
     *
     * @param ExpenseSimpleDoc $newDocument регистрируемый документ
     * @param ExpenseSimpleDoc $oldDocument старый регистрируемый документ
     * @param \Doctrine\ORM\entityManager $em менеджер сущностей
     * @param \AppBundle\Entity\User $currentUser пользователь, регистрирующий документ
     * @throws ConflictHttpException
     * @throws BadRequestHttpException
     */
    public function update(ExpenseSimpleDoc $newDocument, ExpenseSimpleDoc $oldDocument, \Doctrine\ORM\entityManager $em, \AppBundle\Entity\User $currentUser)
    {

        // Общие проверки документа на корректность заполнения

        if (!empty($oldDocument->getCompletedAt()))
            throw new ConflictHttpException('Изменение завершенного документа невозможно');

        if (!empty($oldDocument->getRegisteredAt()))
            throw new ConflictHttpException('Изменение зарегистрированного документа невозможно');

        if ($newDocument->getStatusCode() != $oldDocument->getStatusCode()) {

            $newStatus = $this->get('document.status')
                    ->checkNewStatus(ExpenseSimpleDoc::class, $oldDocument->getStatusCode(), $newDocument->getStatusCode());

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
                if (empty($newDocument->getAcceptedBy())) {
                    $newDocument->setAcceptedBy($currentUser->getId());
                    $newDocument->setAcceptedAt(new \DateTime);
                }
                break;

            case 'rejected':
                if (empty($newDocument->getRegisteredBy())) {
                    $newDocument->setRegisteredBy($currentUser->getId());
                    $newDocument->setRegisteredAt(new \DateTime);
                }
                break;

            case 'completed':

                if (empty($newDocument->getItemOfExpensesId()))
                    throw new BadRequestHttpException('Статья расхода должна быть указана.');
                if (empty($newDocument->getAmount()))
                    throw new BadRequestHttpException('Сумма расхода должна быть указана.');
                if (empty($newDocument->getFinancialResourceId()))
                    throw new BadRequestHttpException('Источник финансов должен быть указан.');

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
