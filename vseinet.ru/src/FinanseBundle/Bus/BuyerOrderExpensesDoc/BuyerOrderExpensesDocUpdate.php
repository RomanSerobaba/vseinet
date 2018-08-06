<?php

namespace FinanseBundle\Bus\BuyerOrderExpensesDoc;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use FinanseBundle\Entity\BuyerOrderExpensesDoc;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

trait BuyerOrderExpensesDocUpdate
{

    /**
     * Обновление документа
     *
     * @param \FinanseBundle\Entity\BuyerOrderExpensesDoc  $newDocument  регистрируемый документ
     * @param \FinanseBundle\Entity\BuyerOrderExpensesDoc  $oldDocument  старый регистрируемый документ
     * @param \Doctrine\ORM\EntityManager      $em           менеджер сущностей
     * @param \AppBundle\Entity\User           $currentUser  пользователь, регистрирующий документ
     */
    public function update(BuyerOrderExpensesDoc $newDocument, BuyerOrderExpensesDoc $oldDocument, \Doctrine\ORM\entityManager $em, \AppBundle\Entity\User $currentUser)
    {

        // Общие проверки документа на корректность заполнения

        if (!empty($oldDocument->getCompletedAt()))
            throw new ConflictHttpException('Изменение завершенного документа невозможно');

        if (!empty($oldDocument->getRegisteredAt()))
            throw new ConflictHttpException('Изменение зарегистрированного документа невозможно');

        if ($newDocument->getStatusCode() != $oldDocument->getStatusCode()) {

            // Проверка допустимости измененения статуса документа

            $rsm = new ResultSetMapping();
            $rsm->addScalarResult("name", "name", "string");
            $rsm->addScalarResult("status_code", "statusCode", "string");
            $rsm->addScalarResult("active", "active", "boolean");
            $rsm->addScalarResult("completing", "completing", "boolean");
            $rsm->addScalarResult("available_new_status_code", "availableNewStatusCode", "string");

            $statusQeryText = "
                select
                    name,
                    status_code,
                    active,
                    completing,
                    available_new_status_code
                from buyer_order_expenses_doc_status
                where
                    status_code = :statusCode
                ";

            $newStatus = $em->createNativeQuery($statusQeryText, $rsm)
                    ->setParameter('statusCode', $newDocument->getStatusCode())
                    ->getOneOrNullResult();

            if (empty($newStatus))
                throw new BadRequestHttpException('Новый статус не соответствует документу');

            $oldStatus = $em->createNativeQuery($statusQeryText, $rsm)
                    ->setParameter('statusCode', $oldDocument->getStatusCode())
                    ->getOneOrNullResult();

            if (empty($oldStatus))
                throw new BadRequestHttpException('Старый статус не соответствует документу');

            // проверка возможности перехода на новый статус
            if (!preg_match("/\W" . $newStatus['statusCode'] . "\W/", $oldStatus['availableNewStatusCode'])) {
                throw new ConflictHttpException('Недопустимый новый статус документа');
            }

            if ($newStatus['completing']) {

                $newDocument->setCompletedAt(new \DateTime);
                $newDocument->setCompletedBy($currentUser->getId());
            } else {

                $newDocument->setCompletedAt();
                $newDocument->setCompletedBy();
            }
        }

        // Особые проверки документа на корректность заполнения
        //////////////////////////////////////////////////////

        $em->persist($newDocument);
        $em->flush();
    }

}
