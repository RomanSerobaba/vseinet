<?php

namespace ReservesBundle\Bus\GoodsDecisionDocType\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ReservesBundle\Entity\GoodsDecisionDocType;

class DeleteCommandHandler extends MessageHandler
{
    protected $mySupplay = null;

    public function handle(DeleteCommand $command)
    {
        
        $em = $this->getDoctrine()->getManager();

        $goodsDecisionDocType = $em->getRepository(GoodsDecisionDocType::class)->find($command->id);
        if (!$goodsDecisionDocType instanceof GoodsDecisionDocType) {
            throw new NotFoundHttpException('Тип претензии не найден');
        }
        
        $em->remove($goodsDecisionDocType);
        $em->flush();

        return;
    }

}
