<?php

namespace FinanseBundle\Bus\SupplierOrderExpensesDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FinanseBundle\Entity\SupplierOrderExpensesDoc;

class StatusCommandHandler extends MessageHandler
{

    use \FinanseBundle\Bus\SupplierOrderExpensesDoc\SupplierOrderExpensesDocRegistration;
    use \FinanseBundle\Bus\SupplierOrderExpensesDoc\SupplierOrderExpensesDocUnRegistration;
    use \FinanseBundle\Bus\SupplierOrderExpensesDoc\SupplierOrderExpensesDocUpdate;

    protected $mySupplay = null;

    public function handle(StatusCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $document = $em->getRepository(SupplierOrderExpensesDoc::class)->find($command->id);
        if (!$document instanceof SupplierOrderExpensesDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }

        if ($document->getStatusCode() == $command->statusCode)
            return; // Проверка наличия изменений

        $currentUser = $this->get('user.identity')->getUser();

        $this->unRegistration($document, $em, $currentUser);

        $oldDocument = clone $document;

        $document->setStatusCode($command->statusCode);

        $this->update($document, $oldDocument, $em, $currentUser);

        $this->registration($document, $em, $currentUser);
    }

}
