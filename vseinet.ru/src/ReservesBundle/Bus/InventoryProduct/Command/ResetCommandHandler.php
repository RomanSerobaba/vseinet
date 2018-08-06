<?php

namespace ReservesBundle\Bus\InventoryProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;

class ResetCommandHandler extends MessageHandler
{
    public function handle(ResetCommand $command) 
    {

        $rsm = new ResultSetMapping();
        
        $queryText = "
            delete from inventory_product ip
                where ip.inventory_did = :inventoryDId;
        ";

        $queryDB = $this->getDoctrine()->getManager()->
                createNativeQuery($queryText, $rsm)->
                setParameters(['inventoryDId' => $command->inventoryId]);
            
        $queryDB->execute();
        
        $queryText = "
            
            insert into inventory_product (inventory_did, base_product_id, purchase_price, retail_price, initial_quantity) (

                with all_categories as (
                    select distinct
                        cp.id
                    from category_path cp
                    left join inventory ii
                        on ii.did = :inventoryDId
                    where cp.pid::text in (select * from jsonb_array_elements_text(ii.categories))                        
                ),

                all_wares as (
                    select
                        ii.did,
                        rl.base_product_id,
                        ii.geo_room_id,
                        sum(rl.delta * si.purchase_price) as in_sum,
                        sum(rl.delta) as sum_delta
                    from goods_reserve_register rl
                    left join inventory ii
                        on ii.did = :inventoryDId
                    left join base_product bp
                        on bp.id = rl.base_product_id
                    left join supply_item si on
                        si.id = rl.supply_item_id
                    where
                        bp.category_id in (select * from all_categories)
                        and rl.geo_room_id = ii.geo_room_id
                    group by
                        ii.did,
                        rl.base_product_id,
                        ii.geo_room_id
                    having sum(rl.delta) <> 0
                )

                select
                    aw.did,
                    aw.base_product_id,
                    case when aw.in_sum is null
                        then 0
                        else aw.in_sum/aw.sum_delta end as purchase_price,
                    case when pp.price is null
                        then
                        case when pp_global.price is null
                            then 0
                            else pp_global.price end
                        else pp.price end as retail_price,
                    aw.sum_delta
                from all_wares aw
                left join geo_point gp
                    on gp.id = aw.geo_room_id
                left join product pp
                    on pp.base_product_id = aw.base_product_id
                    and pp.geo_city_id is null
                left join product pp_global
                    on pp_global.base_product_id = aw.base_product_id
                    and pp_global.geo_city_id = gp.geo_city_id
            )
            ";

        $queryDB = $this->getDoctrine()->getManager()->
                createNativeQuery($queryText, $rsm)->
                setParameters(['inventoryDId' => $command->inventoryId]);
            
        $queryDB->execute();
        
    }
}