<?php

namespace FinanseBundle\Bus\FinancialOperationDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FinanseBundle\Entity\FinancialOperationDoc;
use FinanseBundle\Bus\FinancialOperationDoc\FinancialOperationDocRegistration;
use FinanseBundle\Bus\FinancialOperationDoc\FinancialOperationDocUnRegistration;
use FinanseBundle\Bus\FinancialOperationDoc\FinancialOperationDocUpdate;

class StatusCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(StatusCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(FinancialOperationDoc::class)->find($command->id);
        if (!$document instanceof FinancialOperationDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }

        if ($document->getStatusCode() == $command->statusCode) return; // Проверка наличия изменений
        
        $currentUser = $this->get('user.identity')->getUser();
        
        FinancialOperationDocUnRegistration::UnRegistration($document, $em, $currentUser);
        
        $oldDocument = clone $document;
        
        $document->setStatusCode($command->statusCode);
        
        FinancialOperationDocUpdate::Update($document, $oldDocument, $em, $currentUser);
                
        FinancialOperationDocRegistration::registration($document, $em, $currentUser);
        
    }
    
}
