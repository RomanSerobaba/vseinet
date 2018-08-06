<?php

namespace FinanseBundle\Bus\SupplierOrderExpensesDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FinanseBundle\Entity\SupplierOrderExpensesDoc;

class UpdateCommandHandler extends MessageHandler
{

    use \FinanseBundle\Bus\SupplierOrderExpensesDoc\SupplierOrderExpensesDocUnRegistration;
    use \FinanseBundle\Bus\SupplierOrderExpensesDoc\SupplierOrderExpensesDocUpdate;
    use \FinanseBundle\Bus\SupplierOrderExpensesDoc\SupplierOrderExpensesDocRegistration;

    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $document = $em->getRepository(SupplierOrderExpensesDoc::class)->find($command->id);
        if (!$document instanceof SupplierOrderExpensesDoc) {
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
                $document->getAmountBonus() == $command->amountBonus and
                $document->getAmountMutual() == $command->amountMutual and
                $document->getAmount() == $command->amount and
                $document->getItemOfExpensesId() == $command->itemOfExpensesId and
                $document->getExpectedDateExecute() == $command->expectedDateExecute and
                $document->getDescription() == $command->description and
                $document->getFinancialResourceId() == $command->financialResourceId and
                count(array_diff($document->getRelativeDocumentsIds(), $command->relativeDocumentsIds)) == 0 and
                count(array_diff($command->relativeDocumentsIds, $document->getRelativeDocumentsIds())) == 0
        )
            return;

        $document->setTitle($command->title);
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

        $this->update($document, $oldDocument, $em, $currentUser);

        $this->registration($document, $em, $currentUser);
    }

}
