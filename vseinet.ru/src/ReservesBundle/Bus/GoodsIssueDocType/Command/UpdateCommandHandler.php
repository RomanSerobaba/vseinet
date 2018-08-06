<?php

namespace ReservesBundle\Bus\GoodsIssueDocType\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ReservesBundle\Entity\GoodsIssueDocType;

class UpdateCommandHandler extends MessageHandler
{
    protected $mySupplay = null;

    public function handle(UpdateCommand $command)
    {
        
        $em = $this->getDoctrine()->getManager();

        $goodsIssueDocType = $em->getRepository(GoodsIssueDocType::class)->find($command->id);
        if (!$goodsIssueDocType instanceof GoodsIssueDocType) {
            throw new NotFoundHttpException('Тип претензии не найден');
        }
        
        $goodsIssueDocType->setIsActive($command->isActive);
        $goodsIssueDocType->setIsInteractive($command->isInteractive);
        $goodsIssueDocType->setName($command->name);
        $goodsIssueDocType->setByGoods($command->byGoods);
        $goodsIssueDocType->setByClient($command->byClient);
        $goodsIssueDocType->setBySupplier($command->bySupplier);
        $goodsIssueDocType->setMakeIssueReserve($command->makeIssueReserve);

        $em->persist($goodsIssueDocType);
        $em->flush();

        return;
    }

}
