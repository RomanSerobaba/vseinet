<?php

namespace FinanseBundle\Bus\BuyerOrderExpensesDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FinanseBundle\Entity\BuyerOrderExpensesDoc;

class UpdateCommandHandler extends MessageHandler
{

    use \FinanseBundle\Bus\BuyerOrderExpensesDoc\BuyerOrderExpensesDocUnRegistration;
    use \FinanseBundle\Bus\BuyerOrderExpensesDoc\BuyerOrderExpensesDocUpdate;
    use \FinanseBundle\Bus\BuyerOrderExpensesDoc\BuyerOrderExpensesDocRegistration;

    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $document = $em->getRepository(BuyerOrderExpensesDoc::class)->find($command->id);
        if (!$document instanceof BuyerOrderExpensesDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }

        $currentUser = $this->get('user.identity')->getUser();

        $this->unRegistration($document, $em, $currentUser);

        $oldDocument = clone $document;
        $document = new BuyerOrderExpensesDoc();
        if (// Проверка наличия изменений
                $document->getTitle() == $command->title and
                $document->getStatusCode() == $command->statusCode and

                $document->getOrgDepartmentId() == $command->orgDepartmentId and
                $document->getFinancialCounteragentId() == $command->financialCounteragentId and
                $document->getAmount() == $command->amount and
                $document->getToItemOfExpensesId() == $command->toItemOfExpensesId and
                $document->getExpectedDateExecute() == $command->expectedDateExecute and
                $document->getMaturityDatePayment() == $command->maturityDateExecute and
                $document->getDescription() == $command->description and
                $document->getToFinancialResourceId() == $command->toFinancialResourceId
        )
            return;

        $document->setTitle($command->title);
        $document->setStatusCode($command->statusCode);

        $document->setOrgDepartmentId($command->orgDepartmentId);
        $document->setFinancialCounteragentId($command->financialCounteragentId);
        $document->setAmount($command->amount);
        $document->setToItemOfExpensesId($command->toItemOfExpensesId);
        $document->setExpectedDateExecute($command->expectedDateExecute);
        $document->setMaturityDatePayment($command->maturityDateExecute);
        $document->setDescription($command->description);
        $document->setToFinancialResourceId($command->toFinancialResourceId);

        $this->update($document, $oldDocument, $em, $currentUser);

        $this->registration($document, $em, $currentUser);
    }

}
