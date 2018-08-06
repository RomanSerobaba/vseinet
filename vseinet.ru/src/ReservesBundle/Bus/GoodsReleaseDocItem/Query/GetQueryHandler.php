<?php 
namespace ReservesBundle\Bus\GoodsReleaseDocItem\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $queryText = "
            SELECT 
                NEW ReservesBundle\Bus\GoodsReleaseDocItem\Query\DTO\GoodsReleaseDocItem(
                    i.id,
                    i.goodsReleaseDId,
                    i.baseProductId,
                    bp.name,
                    i.goodsPalletId,
                    gp.title,
                    i.orderItemId,
                    i.goodsStateCode,
                    i.quantity,
                    i.initialQuantity
                )
            FROM ReservesBundle:GoodsReleaseDocItem i
            LEFT JOIN ContentBundle\Entity\BaseProduct bp WITH i.baseProductId = bp.id
            LEFT JOIN ReservesBundle\Entity\GoodsPallet gp WITH i.goodsPalletId = gp.id
            WHERE i.goodsReleaseDId = :goodsReleaseDId";

        $queryDB = $this->getDoctrine()->getManager()->
                createQuery($queryText)->
                setParameter('goodsReleaseDId', $query->goodsReleaseId);
        
        return $queryDB->getArrayResult();
    }

}