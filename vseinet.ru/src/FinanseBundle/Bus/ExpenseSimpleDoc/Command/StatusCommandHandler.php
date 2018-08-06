<?php

namespace FinanseBundle\Bus\ExpenseSimpleDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FinanseBundle\Entity\ExpenseSimpleDoc;

class StatusCommandHandler extends MessageHandler
{
    use \FinanseBundle\Bus\ExpenseSimpleDoc\ExpenseSimpleDocUnRegistration;
    use \FinanseBundle\Bus\ExpenseSimpleDoc\ExpenseSimpleDocUpdate;
    use \FinanseBundle\Bus\ExpenseSimpleDoc\ExpenseSimpleDocRegistration;

    protected $mySupplay = null;

    public function handle(StatusCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(ExpenseSimpleDoc::class)->find($command->id);
        if (!$document instanceof ExpenseSimpleDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }

        if ($document->getStatusCode() == $command->statusCode) return; // Проверка наличия изменений
        
        $currentUser = $this->get('user.identity')->getUser();
        
        $this->unRegistration($document, $em, $currentUser);

        $oldDocument = clone $document;
        
        $document->setStatusCode($command->statusCode);
        
        $this->update($document, $oldDocument, $em, $currentUser);

        $this->registration($document, $em, $currentUser);
    }
    
}
