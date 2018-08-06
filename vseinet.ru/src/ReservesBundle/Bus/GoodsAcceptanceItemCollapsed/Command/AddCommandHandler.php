<?php

namespace ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ReservesBundle\Entity\GoodsAcceptance;
use ReservesBundle\Entity\GoodsAcceptanceItem;
use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\AddOn\TempStorage;

class AddCommandHandler extends MessageHandler
{
    protected $mySupplay = null;

    public function handle(AddCommand $command)
    {

        $em = $this->getDoctrine()->getManager();
        
        // Проверка наличия документа в базе данных
        $goodsAcceptance = $em->getRepository(GoodsAcceptance::class)->find($command->goodsAcceptanceId);
        if (!$goodsAcceptance instanceof GoodsAcceptance)
            throw new NotFoundHttpException('Документ не найден');
        
        // Проверка статуса документа в базе данных
        if (!empty($goodsAcceptance->getCompletedAt()))
            throw new ConflictHttpException('Изменение завершенного документа невозможно.');
        
        $goodsAcceptanceItem = new GoodsAcceptanceItem();
        $goodsAcceptanceItem->setGoodsAcceptanceDId($command->goodsAcceptanceId);
        $goodsAcceptanceItem->setBaseProductId($command->id);
        $goodsAcceptanceItem->setGoodsStateCode($command->goodsStateCode);
        $goodsAcceptanceItem->setGoodsPalletId($command->goodsPalletId);
        $goodsAcceptanceItem->setOrderItemId($command->orderItemId);
        $goodsAcceptanceItem->setSupplyItemId($command->supplyItemId);
        $goodsAcceptanceItem->setInitialQuantity($command->quantity);
        $goodsAcceptanceItem->setQuantity($command->quantity);

        $em->persist($goodsAcceptanceItem);
        
    }
    
}
