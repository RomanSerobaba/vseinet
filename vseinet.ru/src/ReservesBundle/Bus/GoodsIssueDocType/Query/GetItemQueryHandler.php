<?php 

namespace ReservesBundle\Bus\GoodsIssueDocType\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ReservesBundle\Entity\GoodsIssueDocType;

class GetItemQueryHandler extends MessageHandler
{
    public function handle(GetItemQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $goodsIssueDocType = $em->getRepository(GoodsIssueDocType::class)->find($query->id);
        if (!$goodsIssueDocType instanceof GoodsIssueDocType) {
            throw new NotFoundHttpException('Тип претензии не найден');
        }
        
        return $goodsIssueDocType;
    }

}