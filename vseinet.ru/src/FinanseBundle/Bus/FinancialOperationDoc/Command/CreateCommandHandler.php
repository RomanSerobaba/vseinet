<?php

namespace FinanseBundle\Bus\FinancialOperationDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use FinanseBundle\Entity\FinancialOperationDoc;
use FinanseBundle\Entity\FinancialOperationDocRelatedDocument;
use FinanseBundle\Bus\FinancialOperationDoc\FinancialOperationDocRegistration;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {

        $currentUser = $this->get('user.identity')->getUser();
        $em = $this->getDoctrine()->getManager();

        if ('receiving' == $command->operationCode && $command->amount <= 0)
            throw new BadRequestHttpException('Сумма пополнения источника финансов должна быть больше нуля');

        if ('sending' == $command->operationCode && $command->amount >= 0)
            throw new BadRequestHttpException('Сумма изъятия из источника финансов должна быть меньше нуля');

        if ('transfer' == $command->operationCode && !empty($command->parentDocumentId)) {

            $qeryText = "
                select
                    sum(amount) as amount
                from (
                    select
                        amount
                    from financial_operation_doc
                    where
                        did = :parentDocumentId

                    union

                    select
                        amount
                    from bank_operation_doc
                    where
                        did = :parentDocumentId
                )
                ";

            $parentAmount = $em->createNativeQuery($qeryText, new ResultSetMapping())
                    ->setParameter('parentDocumentId', $command->parentDocumentId)
                    ->getSingleScalarResult();

            if (0 != ($parentAmount + $command->amount))
                throw new BadRequestHttpException('Сумма перевода некорректна. Указано '. abs($command->amount) .", а ожидалось ". abs($parentAmount));

        }

        // Получение номера документа

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id', 'integer');
        $documentNumber = $em->createNativeQuery(
            "select nextval('financial_operation_doc_number_seq'::regclass) as id;",
            $rsm)
            ->getSingleScalarResult();

        // Создание документа

        $document = new FinancialOperationDoc();

        $document->setParentDocumentId($command->parentDocumentId);
        $document->setCreatedBy($currentUser->getId());
        $document->setNumber($documentNumber);
        $document->setStatusCode('new');
        $document->setFinancialResourceId($command->financialResourceId);
        $document->setOperationCode($command->operationCode);
        $document->setAmount($command->amount);

        if (!empty($command->title)) {
            $document->setTitle($command->title);
        }else{
            $document->setTitle("Документ №". $documentNumber);
        }

        $em->persist($document);
        $em->flush();

        if (!empty($command->relatedDocuments)) {

            foreach ($command->relatedDocuments as $relatedDocument) {

                $relatedDocument = new FinancialOperationDocRelatedDocument();

                $relatedDocument->setFinancialOperationDocumentId($document->getDId());
                $relatedDocument->setRelatedDocumentId($relatedDocument->documentId);
                $relatedDocument->setAmount($relatedDocument->amount);
                $em->persist($relatedDocument);

            }
            $em->flush();

        }

        $this->get('uuid.manager')->saveId($command->uuid, $document->getDId());

        FinancialOperationDocRegistration::Registration($document, $em, $currentUser);

    }

}
