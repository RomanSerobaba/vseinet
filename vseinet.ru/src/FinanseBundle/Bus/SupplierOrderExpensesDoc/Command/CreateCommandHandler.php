<?php

namespace FinanseBundle\Bus\SupplierOrderExpensesDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;
use FinanseBundle\Entity\SupplierOrderExpensesDoc;

class CreateCommandHandler extends MessageHandler
{

    use \FinanseBundle\Bus\SupplierOrderExpensesDoc\SupplierOrderExpensesDocRegistration;

    public function handle(CreateCommand $command)
    {

        $currentUser = $this->get('user.identity')->getUser();
        $em = $this->getDoctrine()->getManager();

        $documentNumber = $this->get('document.number')->nextValue(SupplierOrderExpensesDoc::class);

        // Создание документа

        $document = new SupplierOrderExpensesDoc();

        $document->setParentDocumentId($command->parentDocumentId);
        $document->setCreatedBy($currentUser->getId());
        $document->setNumber($documentNumber);
        $document->setStatusCode($command->statusCode);

        $document->setOrgDepartmentId($command->orgDepartmentId);
        $document->setFinancialCounteragentId($command->financialCounteragentId);
        $document->setAmountBonus($command->amountBonus);
        $document->setAmountMutual($command->amountMutual);
        $document->setAmount($command->amount);
        $document->setItemOfExpensesId($command->itemOfExpensesId);
        $document->setExpectedDateExecute($command->expectedDateExecute);
        $document->setDescription($command->description);
        $document->setFinancialResourceId($command->financialResourceId);
        $document->setRelativeDocumentsIds($command->relativeDocumentsIds);

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
