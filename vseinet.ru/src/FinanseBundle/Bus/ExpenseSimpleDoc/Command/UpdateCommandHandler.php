<?php

namespace FinanseBundle\Bus\ExpenseSimpleDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FinanseBundle\Entity\ExpenseSimpleDoc;

class UpdateCommandHandler extends MessageHandler
{

    use \FinanseBundle\Bus\ExpenseSimpleDoc\ExpenseSimpleDocUnRegistration;
    use \FinanseBundle\Bus\ExpenseSimpleDoc\ExpenseSimpleDocUpdate;
    use \FinanseBundle\Bus\ExpenseSimpleDoc\ExpenseSimpleDocRegistration;

    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $document = $em->getRepository(ExpenseSimpleDoc::class)->find($command->id);
        if (!$document instanceof ExpenseSimpleDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }

        $currentUser = $this->get('user.identity')->getUser();

        $this->unRegistration($document, $em, $currentUser);

        $oldDocument = clone $document;
        if (// Проверка наличия изменений
                $document->getTitle() == $command->title and
                $document->getStatusCode() == $command->statusCode and
                $document->getOrgDepartmentId() == $command->orgDepartmentId and
                $document->getEquipmentId() == $command->equipmentId and
                $document->getAmount() == $command->amount and
                $document->getItemOfExpensesId() == $command->itemOfExpensesId and
                $document->getExpectedDateExecute() == $command->expectedDateExecute and
                $document->getDescription() == $command->description and
                $document->getFinancialResourceId() == $command->financialResourceId
        )
            return;

        $document->setTitle($command->title);
        $document->setStatusCode($command->statusCode);

        $document->setOrgDepartmentId($command->orgDepartmentId);
        $document->setEquipmentId($command->equipmentId);
        $document->setAmount($command->amount);
        $document->setItemOfExpensesId($command->itemOfExpensesId);
        $document->setExpectedDateExecute($command->expectedDateExecute);
        $document->setDescription($command->description);
        $document->setFinancialResourceId($command->financialResourceId);

        $this->update($document, $oldDocument, $em, $currentUser);

        $this->registration($document, $em, $currentUser);

        $em->persist($document);
        $em->flush();
    }

}
