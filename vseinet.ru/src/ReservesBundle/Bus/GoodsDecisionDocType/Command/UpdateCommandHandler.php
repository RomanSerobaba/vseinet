<?php

namespace ReservesBundle\Bus\GoodsDecisionDocType\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ReservesBundle\Entity\GoodsDecisionDocType;

class UpdateCommandHandler extends MessageHandler
{
    protected $mySupplay = null;

    public function handle(UpdateCommand $command)
    {
        
        $em = $this->getDoctrine()->getManager();

        $goodsDecisionDocType = $em->getRepository(GoodsDecisionDocType::class)->find($command->id);
        if (!$goodsDecisionDocType instanceof GoodsDecisionDocType) {
            throw new NotFoundHttpException('Тип претензии не найден');
        }

        $goodsDecisionDocType->setIsActive($command->isActive);
        $goodsDecisionDocType->setGoodsIssueDocTypeid($command->goodsIssueDocTypeId);
        $goodsDecisionDocType->setName($command->name);
        $goodsDecisionDocType->setByGoods($command->byGoods);
        $goodsDecisionDocType->setByClient($command->byClient);
        $goodsDecisionDocType->setBySupplier($command->bySupplier);
        $goodsDecisionDocType->setNeedGeoRoomId($command->needGeoRoomId);
        $goodsDecisionDocType->setNeedBaseProductId($command->needBaseProductId);
        $goodsDecisionDocType->setNeedPrice($command->needPrice);
        $goodsDecisionDocType->setNeedMoneyBack($command->needMoneyBack);

        $em->persist($goodsDecisionDocType);
        $em->flush();

        return;
    }

}
