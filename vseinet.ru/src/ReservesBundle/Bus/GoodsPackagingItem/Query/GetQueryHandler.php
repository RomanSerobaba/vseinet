<?php 

namespace ReservesBundle\Bus\GoodsPackagingItem\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
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
            WHERE i.goodsPackagingDId = :goodsPackagingDId";

        $queryDB = $this->getDoctrine()->getManager()->
                createQuery($queryText)->
                setParameter('goodsPackagingDId', $query->goodsPackagingId);
        
        return $queryDB->getArrayResult();
    }

}