<?php

namespace ReservesBundle\Bus\GoodsIssueDocType\Command;

use AppBundle\Bus\Message\MessageHandler;
use ReservesBundle\Entity\GoodsIssueDocType;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $goodsIssueDocType = new GoodsIssueDocType();

        $goodsIssueDocType->setIsActive($command->isActive);
        $goodsIssueDocType->setIsInteractive($command->isInteractive);
        $goodsIssueDocType->setName($command->name);
        $goodsIssueDocType->setByGoods($command->byGoods);
        $goodsIssueDocType->setByClient($command->byClient);
        $goodsIssueDocType->setBySupplier($command->bySupplier);
        $goodsIssueDocType->setMakeIssueReserve($command->makeIssueReserve);

        $em->persist($goodsIssueDocType);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $goodsIssueDocType->getId());

        return;            
    }
}