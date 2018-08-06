<?php

namespace FinanseBundle\Bus\ExpenseSimpleDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;
use FinanseBundle\Entity\ExpenseSimpleDoc;

class CreateCommandHandler extends MessageHandler
{

    use \FinanseBundle\Bus\ExpenseSimpleDoc\ExpenseSimpleDocRegistration;

    public function handle(CreateCommand $command)
    {

        $currentUser = $this->get('user.identity')->getUser();
        $em = $this->getDoctrine()->getManager();

        $documentNumber = $this->get('document.number')->nextValue(ExpenseSimpleDoc::class);

        // Создание документа

        $document = new ExpenseSimpleDoc();

        $document->setParentDocumentId($command->parentDocumentId);
        $document->setCreatedBy($currentUser->getId());
        $document->setNumber($documentNumber);
        $document->setStatusCode('new');

        if (!empty($command->title)) {
            $document->setTitle($command->title);
        } else {
            $document->setTitle("Документ №" . $documentNumber);
        }

        $document->setOrgDepartmentId($command->orgDepartmentId);
        $document->setEquipmentId($command->equipmentId);
        $document->setAmount($command->amount);
        $document->setItemOfExpensesId($command->itemOfExpensesId);
        $document->setExpectedDateExecute($command->expectedDateExecute);
        $document->setDescription($command->description);
        $document->setFinancialResourceId($command->financialResourceId);

        $em->persist($document);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $document->getDId());

        $this->registration($document, $em, $currentUser);
    }

}
