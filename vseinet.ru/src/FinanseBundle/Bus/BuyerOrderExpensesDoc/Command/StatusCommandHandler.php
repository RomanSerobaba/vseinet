<?php

namespace FinanseBundle\Bus\BuyerOrderExpensesDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FinanseBundle\Entity\BuyerOrderExpensesDoc;

class StatusCommandHandler extends MessageHandler
{

    use \FinanseBundle\Bus\BuyerOrderExpensesDoc\BuyerOrderExpensesDocRegistration;
    use \FinanseBundle\Bus\BuyerOrderExpensesDoc\BuyerOrderExpensesDocUnRegistration;
    use \FinanseBundle\Bus\BuyerOrderExpensesDoc\BuyerOrderExpensesDocUpdate;

    protected $mySupplay = null;

    public function handle(StatusCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $document = $em->getRepository(BuyerOrderExpensesDoc::class)->find($command->id);
        if (!$document instanceof BuyerOrderExpensesDoc) {
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
