<?php

namespace FinanseBundle\Bus\BuyerOrderExpensesDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use FinanseBundle\Entity\BuyerOrderExpensesDoc;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command) 
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(BuyerOrderExpensesDoc::class)->find($command->id);
        if (!$document instanceof BuyerOrderExpensesDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }
        
        if (!empty($document->getCompletedAt())) {
            throw new ConflictHttpException('Изменение завершенного документа невозможно');
        }

        if (!empty($document->getRegisteredAt())) {
            throw new ConflictHttpException('Документ проведён');
        }

        $em->remove($document);
        $em->flush();
        
    }
}