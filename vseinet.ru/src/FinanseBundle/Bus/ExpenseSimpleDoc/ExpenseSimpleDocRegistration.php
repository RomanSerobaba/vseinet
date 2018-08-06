<?php

namespace FinanseBundle\Bus\ExpenseSimpleDoc;

use FinanseBundle\Entity\ExpenseSimpleDoc;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use FinanseBundle\Entity\FinancialOperationDoc;
use FinanseBundle\Bus\FinancialOperationDoc\FinancialOperationDocRegistration;
use FinanseBundle\Service\FinancialExpensesRegisterKeys;
use FinanseBundle\Service\FinancialExpensesRegisterResources;
use FinanseBundle\Service\FinancialReserveRegisterKeys;
use FinanseBundle\Service\FinancialReserveRegisterResources;

trait ExpenseSimpleDocRegistration
{

    /**
     * Регистрация документа в учетной системе.
     *
     * @param \FinanseBundle\Entity\ExpenseSimpleDoc  $document     регистрируемый документ
     * @param \Doctrine\ORM\EntityManager      $em           менеджер сущностей
     * @param \AppBundle\Entity\User           $currentUser  пользователь, регистрирующий документ
     */
    public function registration(ExpenseSimpleDoc $document, EntityManager $em, User $currentUser)
    {

        if (!empty($document->getRegisteredAt()))
            throw new ConflictHttpException('Документ уже проведён');

        if (empty($document->getCompletedAt()))
            return;

        switch ($document->getStatusCode()) {

            case 'rejected':
                break;

            case 'completed':

                // Запись в регистр финансовых операций

                $this->get('register.financialExpenses')
                        ->appendRecord(
                                $document->getDId(), new \DateTime, new FinancialExpensesRegisterKeys(
                                $document->getItemOfExpensesId(), $document->getOrgDepartmentId(), null, $document->getEquipmentId()), new FinancialExpensesRegisterResources($document->getAmount()));

                // Создание финансофой операции

                $finDoc = new FinancialOperationDoc();

                $finDoc->setParentDocumentId($document->getDId());
                $finDoc->setCreatedBy($currentUser->getId());
                $finDoc->setNumber($this->get('document.number')->nextValue(FinancialOperationDoc::class));
                $finDoc->setStatusCode('completed');
                $finDoc->setFinancialResourceId($document->getFinancialResourceId());
                $finDoc->setOperationCode($document->getAmount() > 0 ? 'receiving' : 'sending');
                $finDoc->setAmount($document->getAmount());
                $finDoc->setTitle(($document->getAmount() > 0 ? 'Поступление' : 'Расход') . ' денежных средств №' . $document->getNumber());
                $finDoc->setCompletedAt(new \DateTime);
                $finDoc->setCompletedBy($currentUser->getId());
                $finDoc->setRegisteredAt(new \DateTime);
                $finDoc->setRegisteredBy($currentUser->getId());

                $em->persist($finDoc);
                $em->flush();

                // Проведение финансовой операции

                $this->get('register.financialReserve')
                        ->appendRecord(
                                $finDoc->getDid(), $finDoc->getRegisteredAt(), new FinancialReserveRegisterKeys($finDoc->getFinancialResourceId()), new FinancialReserveRegisterResources($finDoc->getAmount()));

                break;

            default:
                throw new BadRequestHttpException('Неизвестный статус документа при регистрации');
        }

        // Отметка об удачной записи
        $document->setRegisteredAt(new \DateTime);
        $document->setRegisteredBy($currentUser->getId());

        $em->persist($document);
        $em->flush();
    }

}
