<?php

namespace ReservesBundle\Bus\GoodsReleaseDocItem\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ReservesBundle\Entity\GoodsReleaseDocItem;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $goodsReleaseItem = $em->getRepository(GoodsReleaseDocItem::class)->findOneBy([
            'goodsReleaseId' => $command->goodsReleaseId,
            'goodsPalletId' => $command->goodsPalletId,
            'baseProductId' => $command->baseProductId,
            'orderItemId' => $command->orderItemId
        ]);
        if ($goodsReleaseItem instanceof GoodsReleaseDocItem) {
            throw new ConflictHttpException('Элемент списка товара уже существует');
        }

        $goodsReleaseItem = new GoodsReleaseDocItem();

        $goodsReleaseItem->setBaseProductId($command->baseProductId);
        $goodsReleaseItem->setGoodsReleaseId($command->goodsReleaseId);
        $goodsReleaseItem->setBaseProductId($command->baseProductId);
        $goodsReleaseItem->setGoodsPalletId($command->goodsPalletId);
        $goodsReleaseItem->setInitialQuantity($command->initialQuantity);
        $goodsReleaseItem->setOrderItemId($command->orderItemId);

        $em->persist($goodsReleaseItem);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $goodsReleaseItem->getId());

        return;            
    }
}