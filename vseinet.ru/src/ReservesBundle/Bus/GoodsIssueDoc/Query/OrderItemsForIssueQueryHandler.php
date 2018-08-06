<?php

namespace ReservesBundle\Bus\GoodsIssueDoc\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;

class OrderItemsForIssueQueryHandler extends MessageHandler
{
    public function handle(OrderItemsForIssueQuery $query)
    {

        $queryText = "
            with
            
                -- получаем список элементов заказа
                
                order_item_ids as (
                    select distinct
                        o.did as order_id,
                        oi.id as order_item_id,
                        oi.base_product_id
                    from order_doc o
                    inner join order_item oi
                    on oi.order_id = o.number -- после доработки заказа поменять на oi.order_did = o.did
                    where o.number = {$query->orderNumber}
                ),
                
                -- получаем остатки выданых элементов заказов из учетной системы
                
                summary_from_registers as (
                
                    select
                        sf.order_item_id,
                        sf.supply_item_id,
                        sum(sf.delta) as quantity
                    from (
                    
                        -- товар петреданный покупателю
                        
                        select
                            sr.order_item_id,
                            sr.supply_item_id,
                            sr.delta
                        from sales_register sr
                        where
                            sr.order_item_id in (select order_item_id from order_item_ids)

                        union all

                        -- товар в активных претензиях
                        
                        select
                            gid.order_item_id,
                            gid.supply_item_id,
                            - gir.delta_client AS delta
                        from goods_issue_doc gid
                        inner join goods_issue_register gir on gid.did = gir.registrator_did
                        where
                            gid.order_item_id in (select order_item_id from order_item_ids)
                    ) sf
                    group by 
                        sf.order_item_id,
                        sf.supply_item_id
                    
                )
                
                -- объединяем список элементов с остатками
                
                select 
                    a.base_product_id as id,
                    bp.name,
                    a.order_id,
                    a.order_item_id,
                    b.supply_item_id,
                    b.quantity
                from order_item_ids a
                inner join summary_from_registers b on b.order_item_id = a.order_item_id
                left join base_product bp on bp.id = a.base_product_id
            ";

        $em = $this->getDoctrine()->getManager();

        $result = $em->createNativeQuery($queryText, new DTORSM(DTO\OrderItemsForIssueDTO::class, DTORSM::ARRAY_INDEX))
                ->getResult('DTOHydrator');


        return $result;
    }

}