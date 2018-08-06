<?php

namespace FinanseBundle\Bus\SupplierOrderExpensesDoc;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use FinanseBundle\Entity\SupplierOrderExpensesDoc;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

trait SupplierOrderExpensesDocUpdate
{

    /**
     * Обновление документа
     *
     * @param \FinanseBundle\Entity\SupplierOrderExpensesDoc  $newDocument  регистрируемый документ
     * @param \FinanseBundle\Entity\SupplierOrderExpensesDoc  $oldDocument  старый регистрируемый документ
     * @param \Doctrine\ORM\EntityManager      $em           менеджер сущностей
     * @param \AppBundle\Entity\User           $currentUser  пользователь, регистрирующий документ
     */
    public function update(SupplierOrderExpensesDoc $newDocument, SupplierOrderExpensesDoc $oldDocument, \Doctrine\ORM\entityManager $em, \AppBundle\Entity\User $currentUser)
    {

        // Общие проверки документа на корректность заполнения

        if (!empty($oldDocument->getCompletedAt()))
            throw new ConflictHttpException('Изменение завершенного документа невозможно');

        if (!empty($oldDocument->getRegisteredAt()))
            throw new ConflictHttpException('Изменение зарегистрированного документа невозможно');

        if ($newDocument->getStatusCode() != $oldDocument->getStatusCode()) {

            $newStatus = $this->get('document.status')
                    ->checkNewStatus(SupplierOrderExpensesDoc::class, $oldDocument->getStatusCode(), $newDocument->getStatusCode());

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
