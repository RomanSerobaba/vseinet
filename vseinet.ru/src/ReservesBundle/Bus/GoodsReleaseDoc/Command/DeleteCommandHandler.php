<?php

namespace ReservesBundle\Bus\GoodsReleaseDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ReservesBundle\Entity\GoodsReleaseDoc;
use ReservesBundle\Bus\GoodsReleaseDoc\GoodsReleaseDocUnRegistration;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(GoodsReleaseDoc::class)->find($command->id);
        if (!$document instanceof GoodsReleaseDoc) {
            throw new NotFoundHttpException('Документ выдачи товара не найден');
        }
        
        if (!empty($document->getCompletedAt())) {
            throw new ConflictHttpException('Удаление завершённого документа не возможно.');
        }

        GoodsReleaseDocUnRegistration::UnRegistration($document, $em, $currentUser = $this->get('user.identity')->getUser());
                
        $em->remove($document);
        $em->flush();
    }
}