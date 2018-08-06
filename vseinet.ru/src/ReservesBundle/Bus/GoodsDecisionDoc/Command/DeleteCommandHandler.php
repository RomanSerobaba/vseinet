<?php

namespace ReservesBundle\Bus\GoodsDecisionDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ReservesBundle\Entity\GoodsDecisionDoc;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command) 
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(GoodsDecisionDoc::class)->find($command->id);
        if (!$document instanceof GoodsDecisionDoc) {
            throw new NotFoundHttpException('Документ не найден (команда)');
        }
        
        if (!empty($document->getCompletedAt())) {
            throw new ConflictHttpException('Изменение завершенного документа невозможно (команда)');
        }

        if (!empty($document->getRegisteredAt())) {
            throw new ConflictHttpException('Документ проведён (команда)');
        }

        $em->remove($document);
        $em->flush();
        
    }
}