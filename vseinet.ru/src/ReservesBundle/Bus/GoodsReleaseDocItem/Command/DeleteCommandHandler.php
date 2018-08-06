<?php

namespace ReservesBundle\Bus\GoodsReleaseDocItem\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ReservesBundle\Entity\GoodsReleaseDocItem;

class DeleteCommandHandler extends MessageHandler
{
    protected $mySupplay = null;

    public function handle(DeleteCommand $command)
    {
        
        $em = $this->getDoctrine()->getManager();

        $goodsReleaseDocItem = $em->getRepository(GoodsReleaseDocItem::class)->find($command->id);
        if (!$goodsReleaseDocItem instanceof GoodsReleaseDocItem) {
            throw new NotFoundHttpException('Строка документа не найдена');
        }
        
        // Проверка статуса документа в базе данных
        $goodsReleaseDoc = $em->getRepository(GoodsReleaseDoc::class)->find($goodsReleaseDocItem->getGoodsAcceptanceId());
        if (!empty($goodsReleaseDoc->getCompletedAt())) {
            throw new ConflictHttpException('Изменение завершенного документа невозможно.');
        }

        $em->remove($goodsReleaseDocItem);
        $em->flush();

        return;
    }

}
