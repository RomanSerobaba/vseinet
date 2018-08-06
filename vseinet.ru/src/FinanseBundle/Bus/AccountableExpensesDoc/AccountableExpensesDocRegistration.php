<?php

namespace FinanseBundle\Bus\AccountableExpensesDoc;

use FinanseBundle\Entity\AccountableExpensesDoc;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use FinanseBundle\Service\FinancialExpensesRegisterKeys;
use FinanseBundle\Service\FinancialExpensesRegisterResources;
use FinanseBundle\Service\FinancialMutualRegisterKeys;
use FinanseBundle\Service\FinancialMutualRegisterResources;

trait AccountableExpensesDocRegistration
{

    /**
     * Регистрация документа в учетной системе.
     *
     * @param \FinanseBundle\Entity\AccountableExpensesDoc  $document     регистрируемый документ
     * @param \Doctrine\ORM\EntityManager      $em           менеджер сущностей
     * @param \AppBundle\Entity\User           $currentUser  пользователь, регистрирующий документ
     */
    public function registration(AccountableExpensesDoc $document, EntityManager $em, User $currentUser)
    {

        if (!empty($document->getRegisteredAt()))
            throw new ConflictHttpException('Документ уже проведён');

//        if (empty($document->getCompletedAt()))
//            return;
        // Регистрация в учётной системе

        switch ($newDocument->getStatusCode()) {

            case 'new':
                break;

            case 'active':
                break;

            case 'rejected':
                break;

            case 'wait':

                $this->registrationWait($document, $em, $currentUser);

                break;

            case 'completed':

                $this->registrationWait($document, $em, $currentUser);

                break;

            default:
                throw new BadRequestHttpException('Неизвестный статус документа');
                break;
        }

        // Отметка об удачной записи
        $document->setRegisteredAt(new \DateTime);
        $document->setRegisteredBy($currentUser->getId());

        $em->persist($document);
        $em->flush();
    }

    public function registrationWait(AccountableExpensesDoc $document, EntityManager $em, User $currentUser)
    {
        $this->get('register.financialMutual')
                ->appendRecord(
                        $document->getDId(), $document->getPaymentAt(), new FinancialMutualRegisterKeys(
                        $document->getFinancialCounteragentId(), $document->getDId()));

        // Создание финансофой операции

        $finDoc = $em->getRepository(FinancialOperationDoc::class)->findOneBy(["parentDocumentId" => $document->getDId()]);

        if (!$finDoc instanceof FinancialOperationDoc) {

            $finDoc = new FinancialOperationDoc();

            $finDoc->setParentDocumentId($document->getDId());
            $finDoc->setCreatedBy($currentUser->getId());
            $finDoc->setNumber($this->get('document.number')->nextValue(FinancialOperationDoc::class));
            $finDoc->setStatusCode('completed');
            $finDoc->setFinancialResourceId($document->getFinancialResourceId());
            $finDoc->setOperationCode($document->getAmount() > 0 ? 'receiving' : 'sending');
            $finDoc->setAmount(-abs($document->getAmount()));
            $finDoc->setTitle('Расход денежных средств №' . $document->getNumber());
            $finDoc->setCompletedAt(new \DateTime);
            $finDoc->setCompletedBy($currentUser->getId());
            $finDoc->setRegisteredAt(new \DateTime);
            $finDoc->setRegisteredBy($currentUser->getId());

            $em->persist($finDoc);
            $em->flush();
        }

        // Проведение финансовой операции

        $this->get('register.financialReserve')
                ->appendRecord(
                        $finDoc->getDid(), $finDoc->getRegisteredAt(), new FinancialReserveRegisterKeys($finDoc->getFinancialResourceId()), new FinancialReserveRegisterResources($finDoc->getAmount()));
    }

}
