<?php

namespace FinanseBundle\Bus\BankOperationDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FinanseBundle\Entity\BankOperationDoc;
use FinanseBundle\Bus\BankOperationDoc\BankOperationDocUnRegistration;
use FinanseBundle\Bus\BankOperationDoc\BankOperationDocUpdate;
use FinanseBundle\Bus\BankOperationDoc\BankOperationDocRegistration;

class UpdateCommandHandler extends MessageHandler
{
    
    public function handle(UpdateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(BankOperationDoc::class)->find($command->id);
        if (!$document instanceof BankOperationDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }
        
        $currentUser = $this->get('user.identity')->getUser();
        
        BankOperationDocUnRegistration::UnRegistration($document, $em, $currentUser);
        
        $oldDocument = clone $document;
        $document = new BankOperationDoc();
        if (    // Проверка наличия изменений
                $document->getTitle() == $command->title and
                $document->getStatusCode() == $command->statusCode
                ) return;

        $document->setTitle($command->title);
        $document->setStatusCode($command->statusCode);
        
        // Удаление старого списка связанных документов

        $documentNumber = $em->createNativeQuery(
            "delete from bank_operation_doc_related_document where bank_operation_doc_did = {$command->id};",
            new ResultSetMapping())->execute();

        // Проверка и запись нового списка связанных документов
            
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
        
        BankOperationDocUpdate::Update($document, $oldDocument, $em, $currentUser);
                
        BankOperationDocRegistration::Registration($document, $em, $currentUser);
        
    }
    
}
