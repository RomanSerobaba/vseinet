<?php

namespace ReservesBundle\Bus\GoodsPackaging\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ReservesBundle\Entity\GoodsPackaging;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $item = $em->getRepository(GoodsPackaging::class)->find($command->id);
        if (!$item instanceof GoodsPackaging) {
            throw new NotFoundHttpException('Документ комплектации/разкомплектации не найден');
        }
        
        // Проверка статуса документа
        if (!empty($item->getCompletedAt())) {
            throw new ConflictHttpException('Удаление утвержденного документа не возможно.');
        }

        $em->remove($item);
        $em->flush();
    }
}