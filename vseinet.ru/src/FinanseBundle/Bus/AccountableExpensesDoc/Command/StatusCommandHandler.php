<?php

namespace FinanseBundle\Bus\AccountableExpensesDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FinanseBundle\Entity\AccountableExpensesDoc;
use FinanseBundle\Bus\AccountableExpensesDoc\AccountableExpensesDocRegistration;
use FinanseBundle\Bus\AccountableExpensesDoc\AccountableExpensesDocUnRegistration;
use FinanseBundle\Bus\AccountableExpensesDoc\AccountableExpensesDocUpdate;

class StatusCommandHandler extends MessageHandler
{

    use \FinanseBundle\Bus\AccountableExpensesDoc\AccountableExpensesDocRegistration;
    use \FinanseBundle\Bus\AccountableExpensesDoc\AccountableExpensesDocUnRegistration;
    use \FinanseBundle\Bus\AccountableExpensesDoc\AccountableExpensesDocUpdate;

    protected $mySupplay = null;

    public function handle(StatusCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $document = $em->getRepository(AccountableExpensesDoc::class)->find($command->id);
        if (!$document instanceof AccountableExpensesDoc) {
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
