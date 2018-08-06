<?php 
/*
 * ToDo Убрать лимит 2000
 */
namespace RegisterBundle\Bus\GoodsReserveRegister\Query;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;

class RemnantsQueryHandler extends MessageHandler
{
    public function handle(RemnantsQuery $query)
    {

        if (!empty($query->actualDate)) {
            $setParams = ['actualDate' => $query->actualDate];
        }else{
            $setParams = ['actualDate' => new \DateTime];
        }
        
        $em = $this->getDoctrine()->getManager();
        
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult("last_agg_date", "lastAggDate", "datetime");
        
        $queryText = "
            select
                max(registered_at) as last_agg_date
            from goods_reserve_register_agg*
            where
                registered_at < :actualDate
            ";
                
        $resLastAggDate = $em->createNativeQuery($queryText, $rsm)->
                setParameters($setParams)->
                getOneOrNullResult();

        if (!empty($query->inBaseProductsIds)) {
            $setParams['inBaseProductsIds'] = $query->inBaseProductsIds;
        }

        if (!empty($query->inGeoRoomsIds)) {
            $setParams['inGeoRoomsIds'] = $query->inGeoRoomsIds;
        }

        if (!empty($query->inOrdersItemsIds)) {
            $setParams['inOrdersItemsIds'] = $query->inOrdersItemsIds;
        }

        if (!empty($query->inSypplyItemsIds)) {
            $setParams['inSypplyItemsIds'] = $query->inSypplyItemsIds;
        }

        if (!empty($query->inGoodsConditionsCodes)) {
            $setParams['inGoodsConditionsCodes'] = $query->inGoodsConditionsCodes;
        }
        
        $queryGroupBy = [];
        
        foreach ($query->groupBy as $groupBy) {
            switch ($groupBy) {
                case "geoRoom":
                    $queryGroupBy[] = "geo_room_id";
                    break;

                case "baseProduct":
                    $queryGroupBy[] = "base_product_id";
                    break;

                case "orderItem":
                    $queryGroupBy[] = "order_item_id";
                    break;

                case "supplyItem":
                    $queryGroupBy[] = "supply_item_id";
                    break;

                case "goodsConditionCode":
                    $queryGroupBy[] = "goods_condition_code";
                    break;
            } 
        }
        
        $queryText = "
        with remnants as (
            select";
        
        foreach ($queryGroupBy as $fieldName) {
            $queryText .= "
                pre.{$fieldName},";
        }

        $queryText .= "
                sum(pre.delta) as quantity
            from (";
        
        if (!empty($resLastAggDate)) {
            
            $setParams['lastAggDate'] = $resLastAggDate['lastAggDate'];
            
            $queryText .= "
                (
                select";
            
            foreach ($queryGroupBy as $fieldName) {
                $queryText .= "
                    agg.{$fieldName},";
            }
            
            $queryText .= "
                    agg.delta
                from goods_reserve_register_agg* agg";
            
            
            $queryText .= "
                where agg.registered_at = :lastAggDate";

            if (!empty($query->inBaseProductsIds)) {
                $queryText .= "
                    and agg.base_product_id in (:inBaseProductsIds)";
            }
            
            if (!empty($query->inGeoRoomsIds)) {
                $queryText .= "
                    and agg.geo_room_id in (:inGeoRoomsIds)";
            }
            
            if (!empty($query->inOrdersItemsIds)) {
                $queryText .= "
                    and agg.order_item_id in (:inOrdersItemsIds)";
            }

            if (!empty($query->inSypplyItemsIds)) {
                $queryText .= "
                    and agg.supply_item_id in (:inSypplyItemsIds)";
            }
            
            if (!empty($query->inGoodsConditionsCodes)) {
                $queryText .= "
                    and agg.goods_condition_code in (:inGoodsConditionsCodes)";
            }
            
            $queryText .= "
                )
                union all
                (
                ";
        }
        
        $queryText .= "
                select";

        foreach ($queryGroupBy as $fieldName) {
            $queryText .= "
                    grr.{$fieldName},";
        }

        $queryText .= "
                    grr.delta
                from goods_reserve_register grr
                where grr.registered_at <= :actualDate";

        if (!empty($resLastAggDate)) {
            $queryText .= "
                    and grr.registered_at >= :lastAggDate";
        }

        if (!empty($query->inBaseProductsIds)) {
            $queryText .= "
                    and grr.base_product_id in (:inBaseProductsIds)";
        }

        if (!empty($query->inGeoRoomsIds)) {
            $queryText .= "
                    and grr.geo_room_id in (:inGeoRoomsIds)";
        }

        if (!empty($query->inOrdersItemsIds)) {
            $queryText .= "
                    and grr.order_item_id in (:inOrdersItemsIds)";
        }

        if (!empty($query->inSypplyItemsIds)) {
            $queryText .= "
                    and grr.supply_item_id in (:inSypplyItemsIds)";
        }
            
        if (!empty($query->inGoodsConditionsCodes)) {
            $queryText .= "
                    and grr.goods_condition_code in (:inGoodsConditionsCodes)";
        }
        
        $queryText .= "
            )
            ) as pre
            group by";
        
        foreach ($queryGroupBy as $key => $fieldName) {
            $queryText .= (!empty($key) ? ","  : ""). "
                pre.{$fieldName}";
        }
        
        $queryText .= "
            having
                sum(pre.delta) <> 0
        )
        
        select 
        ";
        
       foreach ($query->groupBy as $groupBy) {
            switch ($groupBy) {
                case "geoRoom":
                    $queryText .= "
            rmn.geo_room_id,
            concat(gct.name, ', ', gpn.name, ', ', grm.name) as geo_room_name,";
                    break;

                case "baseProduct":
                    $queryText .= "
            rmn.base_product_id,
            bpr.name as base_product_name,";
                    break;
                
                case "orderItem":
                    $queryText .= "
            rmn.order_item_id,
            case when ord.title is not null then ord.title
            else 'Свободный остаток'
            end as order_title,";
                    break;

                case "supplyItem":
                    $queryText .= "
            rmn.supply_item_id,
            case when sui_su.title is not null then sui_su.title
            else
                case when sui_gi.title is not null then sui_gi.title
                else
                    case when sui_gp.title is not null then sui_gp.title
                    else
                        'Партия не определена'
                    end
                end
            end as supply_title,";
                    break;
                
                case "goodsConditionCode":
                    $queryText .= "
            rmn.goods_condition_code,";
                    break;
            } 
        }
        
       $queryText .= "
            rmn.quantity
        from remnants rmn
        ";

       foreach ($query->groupBy as $groupBy) {
            switch ($groupBy) {
                case "geoRoom":
                    $queryText .= "
            inner join geo_room as grm on
                grm.id = rmn.geo_room_id
            inner join geo_point as gpn on
                gpn.id = grm.geo_point_id
            inner join geo_city as gct on
                gct.id = gpn.geo_city_id";
                    break;

                case "baseProduct":
                    $queryText .= "
            inner join base_product as bpr on
                bpr.id = rmn.base_product_id";
                    break;
                
                case "supplyItem":
                    $queryText .= "
            inner join supply_item as sui on
                sui.id = rmn.supply_item_id
            left join supply_doc as sui_su on
                sui.parent_doc_type = 'supply'::document_type_code and sui_su.number = sui.parent_doc_id
            left join goods_issue_doc as sui_gi on
                sui.parent_doc_type = 'goods_issue'::document_type_code and sui_gi.number = sui.parent_doc_id
            left join goods_packaging as sui_gp on
                sui.parent_doc_type = 'goods_packaging'::document_type_code and sui_gp.number = sui.parent_doc_id";
                    break;
                
                case "orderItem":
                    $queryText .= "
            left join order_item as ori on
                ori.id = rmn.order_item_id
            left join order_doc as ord on
                ord.number = ori.order_id";
                   break;

               } 
       }
        
        //die($queryText);
        
        $rsm = new ResultSetMapping();
        
        foreach ($query->groupBy as $groupBy) {
            switch ($groupBy) {
                case "geoRoom":
                    $rsm->addScalarResult("geo_room_id", "geoRoomId", "integer");
                    $rsm->addScalarResult("geo_room_name", "geoRoomName", "string");
                    break;

                case "baseProduct":
                    $rsm->addScalarResult("base_product_id", "baseProductId", "integer");
                    $rsm->addScalarResult("base_product_name", "baseProductName", "string");
                    break;
                
                case "orderItem":
                    $rsm->addScalarResult("order_item_id", "orderItemId", "integer");
                    $rsm->addScalarResult("order_title", "orderTitle", "string");
                    break;

                case "supplyItem":
                    $rsm->addScalarResult("supply_item_id", "supplyItemId", "integer");
                    $rsm->addScalarResult("supply_title", "supplyTitle", "string");
                    break;

               case "goodsConditionCode":
                    $rsm->addScalarResult("goods_condition_code", "goodsConditionCode", "string");
                    break;
           } 
        }

        $rsm->addScalarResult("quantity", "quantity", "integer");

        $queryDB = $em->createNativeQuery($queryText, $rsm)->
                setParameters($setParams)->getResult();

        return $queryDB;
    }

}