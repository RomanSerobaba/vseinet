<?php

namespace ReservesBundle\Bus\GoodsPackagingItem\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ReservesBundle\Entity\GoodsPackagingItem;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $goodsPackagingItem = $em->getRepository(GoodsPackagingItem::class)->findOneBy([
            'goodsPackagingDId' => $command->goodsPackagingId,
            'baseProductId' => $command->baseProductId
        ]);
        if (!$goodsPackagingItem instanceof GoodsPackagingItem) {
            throw new NotFoundHttpException('Элемент списка товара комплектации/разкомплектации не найден');
        }

        $em->remove($goodsPackagingItem);
        $em->flush();
            
        return;            
    }
}