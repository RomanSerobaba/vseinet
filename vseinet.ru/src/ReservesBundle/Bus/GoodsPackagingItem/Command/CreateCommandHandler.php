<?php

namespace ReservesBundle\Bus\GoodsPackagingItem\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ReservesBundle\Entity\GoodsPackagingItem;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $goodsPackagingItem = $em->getRepository(GoodsPackagingItem::class)->findOneBy([
            'goodsPackagingDId' => $command->goodsPackagingId,
            'baseProductId' => $command->baseProductId
        ]);
        if ($goodsPackagingItem instanceof GoodsPackagingItem) {
            throw new ConflictHttpException('Элемент списка товара комплектации/разкомплектации уже существует');
        }
        
        $goodsPackagingItem = new GoodsPackagingItem();
        $goodsPackagingItem->setGoodsPackagingDId($command->goodsPackagingId);
        $goodsPackagingItem->setBaseProductId($command->baseProductId);
        $goodsPackagingItem->setQuantityPerOne($command->quantityPerOne);

        $em->persist($goodsPackagingItem);
        $em->flush();
            
        return;            
    }
}