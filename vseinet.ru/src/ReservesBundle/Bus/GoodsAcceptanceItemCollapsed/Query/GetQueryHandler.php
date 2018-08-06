<?php 
namespace ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Query;

use AppBundle\Bus\Message\MessageHandler;
//use AppBundle\ORM\Query\DTORSM;
use Doctrine\ORM\Query\ResultSetMapping;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {

        $queryText = "
            
            select 
            
                i.goods_acceptance_did as goods_acceptance_id,
                i.base_product_id as id,
                bp.name as name,
                'product' as type,
                case when i.order_item_id is null then gp_null.id
                    else gp.id
                    end as geo_point_id,
                case when i.order_item_id is null then gp_null.code
                    else gp.code
                    end as geo_point_code,
                case when i.order_item_id is null then gp_null.name
                    else gp.name
                    end as geo_point_name,
                i.goods_state_code,
                avg(si.purchase_price) as purchase_price,
                sum(i.initial_quantity) as initial_quantity,
                sum(i.quantity) as quantity
                
            from goods_acceptance_item i
            
            -- наименование товара
            left join base_product bp on i.base_product_id = bp.id

            -- заказ и направление
            left join order_item oi on i.order_item_id = oi.id
            left join \"order\" oo on oi.order_id = oo.id
            left join geo_point gp on oo.geo_point_id = gp.id

            -- направление для строки без заказа ползователя
            left join goods_acceptance_doc ga on ga.did = i.goods_acceptance_did
            left join geo_room gr_null on gr_null.id = ga.geo_room_id
            left join geo_point gp_null on gr_null.geo_point_id = gp_null.id

            -- элемент партии
            left join supply_item si on i.supply_item_id = si.id
            
            where i.goods_acceptance_did = {$query->goodsAcceptanceId}
            
            group by
                i.goods_acceptance_did,
                i.base_product_id,
                bp.name,
                case when i.order_item_id is null then gp_null.id
                    else gp.id
                    end,
                case when i.order_item_id is null then gp_null.code
                    else gp.code
                    end,
                case when i.order_item_id is null then gp_null.name
                    else gp.name
                    end,
                i.goods_state_code
            ";

//        $result = $this->getDoctrine()->getManager()
//                ->createNativeQuery($queryText,
//                        new DTORSM(DTO\GoodsAcceptanceItem::class,
//                                DTORSM::ARRAY_INDEX))
//                ->getResult();

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('goods_acceptance_id', 'goodsAcceptanceId', 'integer');
        $rsm->addScalarResult('id', 'id', 'integer');
        $rsm->addScalarResult('name', 'name', 'string');
        $rsm->addScalarResult('type', 'type', 'string');
        $rsm->addScalarResult('geo_point_id', 'geoPointId', 'integer');
        $rsm->addScalarResult('geo_point_code', 'geoPointCode', 'string');
        $rsm->addScalarResult('geo_point_name', 'geoPointName', 'string');
        $rsm->addScalarResult('goods_state_code', 'goodsStateCode', 'string');
        $rsm->addScalarResult('purchase_price', 'purchasePrice', 'integer');
        $rsm->addScalarResult('initial_quantity', 'initialQuantity', 'integer');
        $rsm->addScalarResult('quantity', 'quantity', 'integer');
        
        $result = $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, $rsm)
                ->getResult();

        return $result;

    }
    
}