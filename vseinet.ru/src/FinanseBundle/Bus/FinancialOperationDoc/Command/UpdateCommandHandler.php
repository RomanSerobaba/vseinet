<?php

namespace FinanseBundle\Bus\FinancialOperationDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use FinanseBundle\Entity\FinancialOperationDoc;
use FinanseBundle\Entity\FinancialOperationDocRelatedDocument;
use FinanseBundle\Bus\FinancialOperationDoc\FinancialOperationDocUnRegistration;
use FinanseBundle\Bus\FinancialOperationDoc\FinancialOperationDocUpdate;
use FinanseBundle\Bus\FinancialOperationDoc\FinancialOperationDocRegistration;

class UpdateCommandHandler extends MessageHandler
{

    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $document = $em->getRepository(FinancialOperationDoc::class)->find($command->id);
        if (!$document instanceof FinancialOperationDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }

        $currentUser = $this->get('user.identity')->getUser();

        FinancialOperationDocUnRegistration::UnRegistration($document, $em, $currentUser);

        $oldDocument = clone $document;
        $document = new FinancialOperationDoc();
        if (    // Проверка наличия изменений
                $document->getTitle() == $command->title and
                $document->getStatusCode() == $command->statusCode
                ) return;

        $document->setTitle($command->title);
        $document->setStatusCode($command->statusCode);

        FinancialOperationDocUpdate::Update($document, $relatedDocuments, $oldDocument, $em, $currentUser);

        FinancialOperationDocRegistration::Registration($document, $em, $currentUser);

    }

}
