<?php

namespace FinanseBundle\Bus\ExpenseSimpleDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use FinanseBundle\Entity\ExpenseSimpleDoc;

class DeleteCommandHandler extends MessageHandler
{

    public function handle(DeleteCommand $command) 
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(ExpenseSimpleDoc::class)->find($command->id);
        if (!$document instanceof ExpenseSimpleDoc) {
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
