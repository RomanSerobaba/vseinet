<?php 

namespace ReservesBundle\Bus\GoodsReleaseDocItem\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetItemQueryHandler extends MessageHandler
{
    public function handle(GetItemQuery $query)
    {
        $queryText = "
            SELECT 
                NEW ReservesBundle\Bus\GoodsReleaseDocItem\Query\DTO\GoodsReleaseDocItem(
                    i.id,
                    i.goodsReleaseId,
                    i.baseProductId,
                    bp.name,
                    i.goodsPalletId,
                    gp.title,
                    i.orderItemId,
                    i.defectType,
                    i.quantity,
                    i.initialQuantity
                )
            FROM ReservesBundle:GoodsReleaseDocItem i
            INNER JOIN ContentBundle:BaseProduct bp WITH i.baseProductId = bp.id
            LEFT JOIN ReservesBundle:GoodsPallet gp WITH i.goodsPalletId = gp.id
            WHERE
                i.goodsReleaseId = :goodsReleaseId and
                i.id = :id";

        $queryDB = $this->getDoctrine()->getManager()->
                createQuery($queryText)->
                setParameters([
                    'goodsReleaseId' => $query->goodsReleaseId,
                    'id' => $query->id
                ]);
        
        $result = $queryDB->getArrayResult();
        if (count($result) == 0) throw new NotFoundHttpException('Элемент не найден');
        
        return $result[0];
    }

}