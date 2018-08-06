<?php

namespace ReservesBundle\Bus\GoodsPackagingItem\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ReservesBundle\Entity\GoodsPackagingItem;

class UpdateCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(UpdateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $goodsPackagingItem = $em->getRepository(GoodsPackagingItem::class)->findOneBy([
            'goodsPackagingDId' => $command->goodsPackagingId,
            'baseProductId' => $command->baseProductId
        ]);
        if (!$goodsPackagingItem instanceof GoodsPackagingItem) {
            throw new NotFoundHttpException('Элемент списка товара комплектации/разкомплектации не найден');
        }
        
        $goodsPackagingItem->setQuantityPerOne($command->quantityPerOne);

        $em->persist($goodsPackagingItem);
        $em->flush();
            
        return;            
    }
    
}
