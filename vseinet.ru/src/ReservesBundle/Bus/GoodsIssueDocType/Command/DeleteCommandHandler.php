<?php

namespace ReservesBundle\Bus\GoodsIssueDocType\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ReservesBundle\Entity\GoodsIssueDocType;

class DeleteCommandHandler extends MessageHandler
{
    protected $mySupplay = null;

    public function handle(DeleteCommand $command)
    {
        
        $em = $this->getDoctrine()->getManager();

        $goodsIssueDocType = $em->getRepository(GoodsIssueDocType::class)->find($command->id);
        if (!$goodsIssueDocType instanceof GoodsIssueDocType) {
            throw new NotFoundHttpException('Тип претензии не найден');
        }
        
        $em->remove($goodsIssueDocType);
        $em->flush();

        return;
    }

}
