<?php

namespace FinanseBundle\Bus\BankOperationDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FinanseBundle\Entity\BankOperationDoc;
use FinanseBundle\Bus\BankOperationDoc\BankOperationDocRegistration;
use FinanseBundle\Bus\BankOperationDoc\BankOperationDocUnRegistration;
use FinanseBundle\Bus\BankOperationDoc\BankOperationDocUpdate;

class StatusCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(StatusCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(BankOperationDoc::class)->find($command->id);
        if (!$document instanceof BankOperationDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }

        if ($document->getStatusCode() == $command->statusCode) return; // Проверка наличия изменений
        
        $currentUser = $this->get('user.identity')->getUser();
        
        BankOperationDocUnRegistration::UnRegistration($document, $em, $currentUser);
        
        $oldDocument = clone $document;
        
        $document->setStatusCode($command->statusCode);
        
        BankOperationDocUpdate::Update($document, $oldDocument, $em, $currentUser);
                
        BankOperationDocRegistration::registration($document, $em, $currentUser);
        
    }
    
}
