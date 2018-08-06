<?php 
namespace ReservesBundle\Bus\GoodsReleaseDocItem\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;

class GetCollapsedListQueryHandler extends MessageHandler
{
    public function handle(GetCollapsedListQuery $query)
    {

        $queryText = "
            select 
                gp.goods_release_id,
                gp.id,
                gp.name,
                gp.type,
                gp.goods_state_code,
                gp.quantity,
                gp.initial_quantity
            from (
                select
                    i.goods_release_did as goods_release_id,
                    i.base_product_id as id,
                    bp.name,
                    'product' as type,
                    i.goods_state_code,
                    sum(i.quantity) as quantity,
                    sum(i.initial_quantity) as initial_quantity
                from goods_release_item i
                left join base_product bp on i.base_product_id = bp.id
                where
                    i.goods_release_did = :goodsReleaseDId
                    and i.goods_pallet_id is null
                group by
                    i.goods_release_did,
                    i.base_product_id,
                    bp.name,
                    i.goods_state_code

                union

                select
                    i.goods_release_did,
                    i.goods_pallet_id,
                    gp.title,
                    'pallet',
                    i.goods_state_code,
                    case when sum(i.quantity) = sum(i.initial_quantity) then 1
                    else 0
                    end,
                    1
                from goods_release_item i
                left join goods_pallet gp on i.goods_pallet_id = gp.id
                WHERE
                    i.goods_release_did = :goodsReleaseDId
                    and i.goods_pallet_id is not null
                GROUP BY 
                    i.goods_release_did,
                    i.goods_pallet_id,
                    gp.title,
                    i.goods_state_code
                ) gp
            order by  
                gp.type,
                gp.name,
                gp.goods_state_code
           ";

        return $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, 
                        new DTORSM(DTO\CollapsedGoodsReleaseDocItem::class, DTORSM::ARRAY_INDEX))
                ->setParameter('goodsReleaseDId', $query->goodsReleaseId)
                ->getResult('DTOHydrator');
        
    }

}