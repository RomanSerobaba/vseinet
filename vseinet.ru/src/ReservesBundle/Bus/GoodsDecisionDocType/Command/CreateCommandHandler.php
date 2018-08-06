<?php

namespace ReservesBundle\Bus\GoodsDecisionDocType\Command;

use AppBundle\Bus\Message\MessageHandler;
use ReservesBundle\Entity\GoodsDecisionDocType;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $goodsDecisionDocType = new GoodsDecisionDocType();

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

        $this->get('uuid.manager')->saveId($command->uuid, $goodsDecisionDocType->getId());

        return;            
    }
}