<?php

namespace FinanseBundle\Bus\BankOperationDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;
use FinanseBundle\Entity\BankOperationDoc;
use FinanseBundle\Entity\BankOperationDocRelatedDocument;
use FinanseBundle\Bus\BankOperationDoc\BankOperationDocRegistration;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        
        $currentUser = $this->get('user.identity')->getUser();
        $em = $this->getDoctrine()->getManager();

        // Получение номера документа

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id', 'integer');
        $documentNumber = $em->createNativeQuery(
            "select nextval('bank_operation_doc_number_seq'::regclass) as id;",
            $rsm)->getSingleScalarResult();

        // Создание документа

        $document = new BankOperationDoc();

        $document->setParentDocumentId($command->parentDocumentId);
        $document->setCreatedBy($currentUser->getId());
        $document->setNumber($documentNumber);
        $document->setStatusCode('new');
        $document->setFinancialResourceId($command->financialResourceId);

        if (!empty($command->title)) {
            $document->setTitle($command->title);
        }else{
            $document->setTitle("Банковский документ №". $documentNumber);
        }

        $em->persist($document);
        $em->flush();

        if (!empty($command->relatedDocuments)) {
            
            foreach ($command->relatedDocuments as $relatedDocument) {

                $relatedDocument = new BankOperationDocRelatedDocument();

                $relatedDocument->setBankOperationDocumentId($document->getDId());
                $relatedDocument->setRelatedDocumentId($relatedDocument->documentId);
                $relatedDocument->setAmount($relatedDocument->amount);
                $em->persist($relatedDocument);

            }
            $em->flush();
            
        }

        $this->get('uuid.manager')->saveId($command->uuid, $document->getDId());

        BankOperationDocRegistration::Registration($document, $em, $currentUser);

    }

}
