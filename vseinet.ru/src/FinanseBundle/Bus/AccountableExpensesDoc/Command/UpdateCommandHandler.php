<?php

namespace FinanseBundle\Bus\AccountableExpensesDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FinanseBundle\Entity\AccountableExpensesDoc;

class UpdateCommandHandler extends MessageHandler
{

    use \FinanseBundle\Bus\AccountableExpensesDoc\AccountableExpensesDocUnRegistration;
    use \FinanseBundle\Bus\AccountableExpensesDoc\AccountableExpensesDocUpdate;
    use \FinanseBundle\Bus\AccountableExpensesDoc\AccountableExpensesDocRegistration;

    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $document = $em->getRepository(AccountableExpensesDoc::class)->find($command->id);
        if (!$document instanceof AccountableExpensesDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }

        $currentUser = $this->get('user.identity')->getUser();

        $this->unRegistration($document, $em, $currentUser);

        $oldDocument = clone $document;
        if (// Проверка наличия изменений
                $document->getTitle() == $command->title and
                $document->getStatusCode() == $command->statusCode and
                $document->getOrgDepartmentId() == $command->orgDepartmentId and
                $document->getFinancialCounteragentId() == $command->financialCounteragentId and
                $document->getAmount() == $command->amount and
                $document->getToItemOfExpensesId() == $command->toItemOfExpensesId and
                $document->getToEquipmentId() == $command->toEquipmentId and
                $document->getExpectedDateExecute() == $command->expectedDateExecute and
                $document->getMaturityDatePayment() == $command->maturityDateExecute and
                $document->getDescription() == $command->description and
                $document->getFinancialResourceId() == $command->financialResourceId
        )
            return;
        $document->setTitle($command->title);
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

        $this->update($document, $oldDocument, $em, $currentUser);

        $this->registration($document, $em, $currentUser);
    }

}
