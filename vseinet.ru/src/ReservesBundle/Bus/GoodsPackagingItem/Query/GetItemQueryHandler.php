<?php 

namespace ReservesBundle\Bus\GoodsPackagingItem\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetItemQueryHandler extends MessageHandler
{
    public function handle(GetItemQuery $query)
    {
        $queryText = "
            SELECT 
                NEW ReservesBundle\Bus\GoodsPackagingItem\Query\DTO\GoodsPackaging(
                    i.goodsPackagingDId,
                    i.baseProductId,
                    bp.name,
                    i.quantityPerOne
                )
            FROM ReservesBundle:GoodsPackagingItem i
            INNER JOIN ContentBundle\Entity\BaseProduct bp WITH i.baseProductId = bp.id
            WHERE
                i.goodsPackagingDId = :goodsPackagingDId and
                i.baseProductId = :baseProductId";

        $queryDB = $this->getDoctrine()->getManager()->
                createQuery($queryText)->
                setParameters([
                    'goodsPackagingDId' => $query->goodsPackagingId,
                    'baseProductId' => $query->baseProductId
                ]);
        
        $result = $queryDB->getArrayResult();
        if (count($result) == 0) throw new NotFoundHttpException('Элемент не найден');
        
        return $result[0];
    }

}