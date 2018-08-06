<?php

namespace FinanseBundle\Bus\AccountableExpensesDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;
use FinanseBundle\Entity\AccountableExpensesDoc;

class CreateCommandHandler extends MessageHandler
{

    use \FinanseBundle\Bus\AccountableExpensesDoc\AccountableExpensesDocRegistration;

    public function handle(CreateCommand $command)
    {

        $currentUser = $this->get('user.identity')->getUser();
        $em = $this->getDoctrine()->getManager();

        $documentNumber = $this->get('document.number')->nextValue(AccountableExpensesDoc::class);

        // Создание документа

        $document = new AccountableExpensesDoc();

        $document->setParentDocumentId($command->parentDocumentId);
        $document->setCreatedBy($currentUser->getId());
        $document->setNumber($documentNumber);
        $document->setStatusCode($command->statusCode);

        $document->setOrgDepartmentId($command->orgDepartmentId);
        $document->setFinancialCounteragentId($command->financialCounteragentId);
        $document->setAmount($command->amount);
        $document->setToItemOfExpensesId($command->toItemOfExpensesId);
        $document->setToEquipmentId($command->toEquipmentId);
        $document->setExpectedDateExecute($command->expectedDateExecute);
        $document->setMaturityDatePayment($command->maturityDateExecute);
        $document->setDescription($command->description);
        $document->setFinancialResourceId($command->financialResourceId);

        if (!empty($command->title)) {
            $document->setTitle($command->title);
        } else {
            $document->setTitle("Выдача денег под отчет №" . $documentNumber);
        }

        $em->persist($document);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $document->getDId());

        $this->registration($document, $em, $currentUser);
    }

}
