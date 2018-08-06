<?php

namespace ReservesBundle\Bus\GoodsAcceptanceItemCollapsed;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

trait DeltaActionTrait
{
    /*
     * Выполнение операции инкремента/декримента
     */
    private function runDelta(Command\DeltaCommand $command, EntityManager $em)
    {
        if (0 < $command->delta && 'normal' == $command->goodsStateCode) {

            $toGeoPoints = $this->incProduct($command, $em);
            
        }elseif (0 > $command->delta && 'normal' == $command->goodsStateCode) {

            $toGeoPoints = $this->decProduct($command, $em);

        }elseif (0 < $command->delta && 'normal' != $command->goodsStateCode) {

            $toGeoPoints = $this->mrkProduct($command, $em);

        }elseif (0 > $command->delta && 'normal' != $command->goodsStateCode) {

            $toGeoPoints = $this->umkProduct($command, $em);

        }else{

            throw new BadRequestHttpException('Непредвиденное сочетание параметров.');

        }
        
        return $toGeoPoints;
        
    }

    /*
     * Приемка позиции товара
     */
    private function incProduct(Command\DeltaCommand $command, EntityManager $em)
    {

        $delta = $command->delta;
        
        $queryText = "
            with recursive

            -- Нормализованный список отгрузки /без паллет
            normal_items (ord, id, need_quantity) as(

                select
                    row_number() over(order by o.type_code ASC, gai.order_item_id ASC)
                        as ord,                                                               -- Синтетический ключ нормализации
                    gai.id,
                    gai.initial_quantity - gai.quantity as need_quantity
                from
                    goods_acceptance_item gai
                inner join order_item oi on oi.id = gai.order_item_id
                inner join \"order\" o on o.id = oi.order_id
                where
                    gai.goods_acceptance_did = {$command->goodsAcceptanceId}                  -- Документ отгрузки
                    and gai.base_product_id = {$command->id}                                  -- Идентификатор продукта
                    and gai.goods_state_code = '{$command->goodsStateCode}'::goods_state_code -- Состояние товара";

        if (!empty($command->geoPointId)) {
            $queryText .= "
                    and o.geo_point_id = {$command->geoPointId}                               -- Направление";
        }

        $queryText .= "
                    and gai.initial_quantity > gai.quantity                                   -- Не принят или принят частично
                order by
                    o.type_code ASC, gai.order_item_id ASC                                    -- Упорядочить по номеру заказа

            ),

            proporcional(ord, id, add_quantity, rest) as (

                select r0.ord, r0.id, r0.add_quantity, r0.rest from (

                    select
                        ni1.ord,
                        ni1.id,
                        least(ni1.need_quantity, {$delta}) as add_quantity,
                        {$delta} - least(ni1.need_quantity, {$delta}) as rest
                    from
                        normal_items ni1
                    order by ni1.ord
                    limit 1

                ) r0

                union all

                select r1.ord, r1.id, r1.add_quantity, r1.rest from (

                    select
                        ni2.ord,
                        ni2.id,
                        least(ni2.need_quantity, pr1.rest) as add_quantity,
                        pr1.rest - least(ni2.need_quantity, pr1.rest) as rest
                    from
                        normal_items ni2,
                        proporcional pr1
                    where
                        ni2.ord > pr1.ord and pr1.rest > 0
                    order by ni2.ord
                    limit 1

                ) r1

            )

            update
                goods_acceptance_item gai
            set
                quantity = gai.quantity + ppc.add_quantity
            from
                (select id, add_quantity from proporcional) ppc
            left join goods_acceptance_item old_gai on old_gai.id = ppc.id
            -- заказ и направление
            left join order_item oi on oi.id = old_gai.order_item_id
            left join \"order\" o on o.id = oi.order_id
            left join geo_point gp on o.geo_point_id = gp.id
            where
                gai.id = ppc.id
                and 0 = (select min(pp2.rest) from proporcional pp2)

            returning o.geo_point_id, ppc.add_quantity, gp.code, gp.name
        ";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('geo_point_id', 'geoPointId', 'integer');
        $rsm->addScalarResult('add_quantity', 'quantity', 'integer');
        $rsm->addScalarResult('code', 'geoPointCode', 'string');
        $rsm->addScalarResult('name', 'quaPointName', 'string');

        $result = $em->createNativeQuery($queryText, $rsm)->getResult();

        if (empty($result)) {
            throw new BadRequestHttpException('Нет товара для отгрузки.');
        }

        return $result;

    }

    /*
     * Возврат (отмена приемки) позиции товара
     */
    private function decProduct(Command\DeltaCommand $command, EntityManager $em)
    {
        $delta = -$command->delta;

        $queryText = "
            with recursive

            -- Нормализованный список отгрузки /без паллет
            normal_items (ord, id, quantity) as(

                select
                    row_number() over(order by o.type_code DESC, gai.order_item_id DESC)
                        as ord,                                                               -- Синтетический ключ нормализации
                    gai.id,
                    gai.quantity as quantity
                from
                    goods_acceptance_item gai
                inner join order_item oi on oi.id = gai.order_item_id
                inner join \"order\" o on o.id = oi.order_id
                where
                    gai.goods_acceptance_did = {$command->goodsAcceptanceId}                  -- Документ отгрузки
                    and gai.base_product_id = {$command->id}                                  -- Идентификатор продукта
                    and gai.goods_state_code = '{$command->goodsStateCode}'::goods_state_code -- Состояние товара";

        if (!empty($command->geoPointId)) {
            $queryText .= "
                    and o.geo_point_id = {$command->geoPointId}                             -- Направление";
        }

        $queryText .= "
                    and gai.quantity > 0                                                      -- Отгружен полностью или частично
                order by
                    o.type_code DESC, gai.order_item_id DESC                                  -- Упорядочить по номеру заказа

            ),


            proporcional(ord, id, rmv_quantity, rest) as (

                select r0.ord, r0.id, r0.rmv_quantity, r0.rest from (

                    select
                        ni1.ord,
                        ni1.id,
                        least(ni1.quantity, {$delta}) as rmv_quantity,
                        {$delta} - least(ni1.quantity, {$delta}) as rest
                    from
                        normal_items ni1
                    order by ni1.ord
                    limit 1

                ) r0

                union all

                select r1.ord, r1.id, r1.rmv_quantity, r1.rest from (

                    select
                        ni2.ord,
                        ni2.id,
                        least(ni2.quantity, pr1.rest) as rmv_quantity,
                        pr1.rest - least(ni2.quantity, pr1.rest) as rest
                    from
                        normal_items ni2,
                        proporcional pr1
                    where
                        ni2.ord > pr1.ord and pr1.rest > 0
                    order by ni2.ord
                    limit 1

                ) r1

            )

            update
                goods_acceptance_item gai
            set
                quantity = gai.quantity - ppc.rmv_quantity
            from
                (select id, rmv_quantity from proporcional) ppc
            left join goods_acceptance_item old_gai on old_gai.id = ppc.id
            -- заказ и направление
            left join order_item oi on oi.id = old_gai.order_item_id
            left join \"order\" o on o.id = oi.order_id
            left join geo_point gp on o.geo_point_id = gp.id
            where
                gai.id = ppc.id
                and 0 = (select min(pp2.rest) from proporcional pp2)

            returning o.geo_point_id, ppc.rmv_quantity, gp.code, gp.name
        ";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('geo_point_id', 'geoPointId', 'integer');
        $rsm->addScalarResult('rmv_quantity', 'quantity', 'integer');
        $rsm->addScalarResult('code', 'geoPointCode', 'string');
        $rsm->addScalarResult('name', 'quaPointName', 'string');

        $items = $em->createNativeQuery($queryText, $rsm)->getArrayResult();

        if (0 == count($items)) {
            throw new BadRequestHttpException('Нет товара для возврата.');
        }

        return $items;
    }

    /*
     * Отметка позиции товара как некачественной (для помещения в претензию, без приемки)
     */
    private function mrkProduct(Command\DeltaCommand $command, EntityManager $em)
    {

        $delta = $command->delta;

        $queryText = "
            with recursive

            -- Нормализованный список отгрузки /без паллет
            normal_items (ord, id, order_item_id, supply_item_id, found_quantity, initial_quantity, quantity) as(

                select
                    row_number() over(order by o.type_code DESC, gai.order_item_id DESC)
                        as ord,                                                               -- Синтетический ключ нормализации
                    gai.id,
                    gai.order_item_id,
                    gai.supply_item_id,
                    gai.initial_quantity - gai.quantity as found_quantity,
                    gai.initial_quantity as initial_quantity,
                    gai.quantity as quantity
                from
                    goods_acceptance_item gai
                inner join order_item oi on oi.id = gai.order_item_id
                inner join \"order\" o on o.id = oi.order_id
                where
                    gai.goods_acceptance_did = {$command->goodsAcceptanceId}                  -- Документ отгрузки
                    and gai.base_product_id = {$command->id}                                  -- Идентификатор продукта
                    and gai.goods_state_code = 'normal'::goods_state_code                     -- Нормальное состояние";

        if (!empty($command->geoPointId)) {
            $queryText .= "
                    and o.geo_point_id = {$command->geoPointId}                             -- Направление";
        }

        $queryText .= "
                    and gai.quantity > 0                                                      -- Отгружен полностью или частично
                order by
                    o.type_code DESC, gai.order_item_id DESC                                  -- Упорядочить по номеру заказа

            ),

            -- Распределение дефекта по списку

            proporcional(ord, id, order_item_id, supply_item_id, rm_quantity, initial_quantity, quantity, rest) as (

                select r0.ord, r0.id, r0.order_item_id, r0.supply_item_id, r0.rm_quantity, r0.initial_quantity, r0.quantity, r0.rest from (

                    select
                        ni1.ord,
                        ni1.id,
                        ni1.order_item_id,
                        ni1.supply_item_id,
                        least(ni1.found_quantity, {$delta}) as rm_quantity,
                        ni1.initial_quantity as initial_quantity,
                        ni1.quantity as quantity,
                        {$delta} - least(ni1.found_quantity, {$delta}) as rest
                    from
                        normal_items ni1
                    order by ni1.ord
                    limit 1

                ) r0

                union all

                select r1.ord, r1.id, r1.order_item_id, r1.supply_item_id, r1.rm_quantity, r1.initial_quantity, r1.quantity, r1.rest from (

                    select
                        ni2.ord,
                        ni2.id,
                        ni2.order_item_id,
                        ni2.supply_item_id,
                        least(ni2.found_quantity, pr1.rest) as rm_quantity,
                        ni2.initial_quantity as initial_quantity,
                        ni2.quantity as quantity,
                        pr1.rest - least(ni2.found_quantity, pr1.rest) as rest
                    from
                        normal_items ni2,
                        proporcional pr1
                    where
                        ni2.ord > pr1.ord and pr1.rest > 0
                    order by ni2.ord
                    limit 1

                ) r1

            ),

            -- Приемник брака

            accept_items (id, order_item_id) as(

                select
                    gai.id,
                    gai.order_item_id
                from goods_acceptance_item gai
                inner join order_item oi on oi.id = gai.order_item_id
                inner join \"order\" o on o.id = oi.order_id
                where
                    gai.goods_acceptance_did = {$command->goodsAcceptanceId}                  -- Документ
                    and gai.base_product_id = {$command->id}                                  -- Идентификатор продукта
                    and gai.goods_state_code = '{$command->goodsStateCode}'::goods_state_code -- Тип дефекта";
                    
        if (!empty($command->geoPointId)) {
            $queryText .= "
                    and o.geo_point_id = {$command->geoPointId}                             -- Направление";
        }
        
            $queryText .= "
                order by o.type_code ASC, order_item_id DESC
            ),

            -- Уменьшить количество в товаре без дефекта

            wup1 as (

                update
                    goods_acceptance_item up1
                set
                    initial_quantity = up1.initial_quantity - pp1.rm_quantity
                from (
                    select
                        id,
                        rm_quantity
                    from
                        proporcional
                    where
                        rm_quantity < initial_quantity) pp1
                left join goods_acceptance_item old_up1 on old_up1.id = pp1.id
                where
                    up1.id = pp1.id
                    and 0 = (select min(rest) from proporcional)
                    
                returning
                    pp1.id as item_id,
                    0 as quantity

            ),

            -- Увеличить количество в товаре с дефектом

            wup2 as (

                update
                    goods_acceptance_item up2
                set
                    quantity = up2.quantity + pp2.rm_quantity,
                    initial_quantity = up2.initial_quantity + pp2.rm_quantity
                from (
                    select
                        ai2.id,
                        p2.rm_quantity
                    from
                        accept_items ai2
                    inner join proporcional p2 on
                        p2.order_item_id = ai2.order_item_id) pp2
                left join goods_acceptance_item old_up2 on old_up2.id = pp2.id
                where
                    up2.id = pp2.id
                    and 0 = (select min(rest) from proporcional)

                returning
                    pp2.id as item_id,
                    pp2.rm_quantity as quantity

            ),

            -- Создание новых записей с дефектом

            win1 as (

                insert into goods_acceptance_item as in1 (
                    goods_acceptance_did,
                    base_product_id,
                    goods_pallet_id,
                    quantity,
                    initial_quantity,
                    order_item_id,
                    supply_item_id,
                    goods_state_code)
                select
                    {$command->goodsAcceptanceId},
                    {$command->id},
                    null,
                    p3.rm_quantity,
                    p3.rm_quantity,
                    p3.order_item_id,
                    p3.supply_item_id,
                    '{$command->goodsStateCode}'::goods_state_code
                from proporcional p3
                left join accept_items ai3 on
                    ai3.order_item_id = p3.order_item_id
                where
                    ai3.order_item_id is null
                    and 0 = (select min(rest) from proporcional)

                returning
                    id as item_id,
                    quantity as quantity

            ),

            -- Удалить полностью отбракованные строки

            wdl1 as (

                delete from
                    goods_acceptance_item dl1
                where
                    dl1.id in (
                        select
                            id
                        from
                            proporcional
                        where
                            rm_quantity = initial_quantity)
                    and 0 = (select min(rest) from proporcional)

                returning
                    id as item_id,
                    0 as quantity

            )

            select 
                res.item_id,
                sum(res.quantity) as quantity
            from (
                select * from wup1
                union all
                select * from wup2
                union all
                select * from win1
                union all
                select * from wdl1
            ) res
            group by
                res.item_id
 
       ";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('item_id', 'itemId', 'integer');
        $rsm->addScalarResult('quantity', 'quantity', 'integer');

        $items = $em->createNativeQuery($queryText, $rsm)->getArrayResult();
        
        if (0 == count($items)) {
            throw new BadRequestHttpException('Нет товара для отметки несоответствующего качества.');
        }
        
        // Сбор направлений отгрузки по результатам выполнения маркировки
        
        $queryText = "
            with

            -- Данные, обработанные предыдущим запросом
            real_items (item_id, quantity) as(
        ";
        
        $firstTurn = true;
        foreach ($items as $item) {
            
            if (0 == $item['quantity']) continue;
            
            if ($firstTurn) {
                $firstTurn = !$firstTurn;
                
                $queryText .= "
                    select
                        {$item['itemId']} as item_id,
                        {$item['quantity']} as quantity
                ";
                        
            }else{
                
                $queryText .= "
                    
                    union all
                    
                    select
                        {$item['itemId']},
                        {$item['quantity']}
                ";
                        
            }
        }
        
        $queryText .= "
            )
        
            select
                o.geo_point_id,
                gp.code,
                gp.name,
                sum(ri.quantity) as quantity
            from real_items ri
            left join goods_acceptance_item gai on ri.item_id = gai.id
            -- заказ и направление
            left join order_item oi on oi.id = gai.order_item_id
            left join \"order\" o on o.id = oi.order_id
            left join geo_point gp
            on o.geo_point_id = gp.id
            
            group by
                o.geo_point_id,
                gp.code,
                gp.name
        ";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('geo_point_id', 'geoPointId', 'integer');
        $rsm->addScalarResult('code', 'geoPointCode', 'string');
        $rsm->addScalarResult('name', 'quaPointName', 'string');
        $rsm->addScalarResult('quantity', 'quantity', 'integer');

        $items = $em->createNativeQuery($queryText, $rsm)->getResult();
        
        return $items;

    }

    /*
     * Отмена отметки позиции товара как некачественной (для возможности дальнейшей отгрузки отгрузки)
     */
    private function umkProduct(Command\DeltaCommand $command, EntityManager $em)
    {
        $delta = -$command->delta;

        $queryText = "
            with recursive

            -- Нормализованный список отгрузки /без паллет
            normal_items (ord, id, order_item_id, supply_item_id, found_quantity, initial_quantity, quantity) as(

                select
                    row_number() over(order by o.type_code ASC, gai.order_item_id ASC)
                        as ord,                                                               -- Синтетический ключ нормализации
                    gai.id,
                    gai.order_item_id,
                    gai.supply_item_id,
                    gai.initial_quantity as found_quantity,
                    gai.initial_quantity as initial_quantity,
                    gai.quantity as quantity
                    
                from goods_acceptance_item gai
                    
                -- нарпавление по умолчанию
                inner join goods_acceptance_doc ga on ga.did = gai.goods_acceptance_did
                inner join geo_room gr on gr.id = ga.geo_room_id
                
                -- реальные направления
                left join order_item oi on oi.id = gai.order_item_id
                left join \"order\" o on o.id = oi.order_id
                
                where
                    gai.goods_acceptance_did = {$command->goodsAcceptanceId}                  -- Документ отгрузки
                    and gai.base_product_id = {$command->id}                                  -- Идентификатор продукта
                    and gai.goods_state_code = '{$command->goodsStateCode}'::goods_state_code -- Нормальное состояние";

        if (!empty($command->geoPointId)) {
            $queryText .= "
                    and (                                                                     -- Направление
                        o.geo_point_id = {$command->geoPointId} or                               -- истинное
                        (o.geo_point_id is null and gr.geo_point_id = {$command->geoPointId}))   -- по умолчанию";        
            }

        $queryText .= "
                    and gai.quantity > 0                                                      -- Отгружен полностью или частично
                order by
                    o.type_code ASC, gai.order_item_id ASC                                    -- Упорядочить по номеру заказа

            ),

            -- Распределение дефекта по списку

            proporcional(ord, id, order_item_id, supply_item_id, rm_quantity, initial_quantity, quantity, rest) as (

                select r0.ord, r0.id, r0.order_item_id, r0.supply_item_id, r0.rm_quantity, r0.initial_quantity, r0.quantity, r0.rest from (

                    select
                        ni1.ord,
                        ni1.id,
                        ni1.order_item_id,
                        ni1.supply_item_id,
                        least(ni1.found_quantity, {$delta}) as rm_quantity,
                        ni1.initial_quantity as initial_quantity,
                        ni1.quantity as quantity,
                        {$delta} - least(ni1.found_quantity, {$delta}) as rest
                    from
                        normal_items ni1
                    order by ni1.ord
                    limit 1

                ) r0

                union all

                select r1.ord, r1.id, r1.order_item_id, r1.supply_item_id, r1.rm_quantity, r1.initial_quantity, r1.quantity, r1.rest from (

                    select
                        ni2.ord,
                        ni2.id,
                        ni2.order_item_id,
                        ni2.supply_item_id,
                        least(ni2.found_quantity, pr1.rest) as rm_quantity,
                        ni2.initial_quantity as initial_quantity,
                        ni2.quantity as quantity,
                        pr1.rest - least(ni2.found_quantity, pr1.rest) as rest
                    from
                        normal_items ni2,
                        proporcional pr1
                    where
                        ni2.ord > pr1.ord and pr1.rest > 0
                    order by ni2.ord
                    limit 1

                ) r1

            ),

            -- Приемник нормальных продуктов

            accept_items (id, order_item_id) as(

                select
                    gai.id,
                    gai.order_item_id
                from goods_acceptance_item gai
                    
                -- нарпавление по умолчанию
                inner join goods_acceptance_doc ga on ga.did = gai.goods_acceptance_did
                inner join geo_room gr on gr.id = ga.geo_room_id
                
                -- реальные направления
                left join order_item oi on oi.id = gai.order_item_id
                left join \"order\" o on o.id = oi.order_id

                where
                    gai.goods_acceptance_did = {$command->goodsAcceptanceId} -- Документ
                    and gai.base_product_id = {$command->id}                 -- Идентификатор продукта
                    and gai.goods_state_code = 'normal'::goods_state_code         -- Тип дефекта";
                    
        if (!empty($command->geoPointId)) {
            $queryText .= "
                    and (                                                                     -- Направление
                        o.geo_point_id = {$command->geoPointId} or                               -- истинное
                        (o.geo_point_id is null and gr.geo_point_id = {$command->geoPointId}))   -- по умолчанию";        
        }
            $queryText .= "
                order by o.type_code ASC, order_item_id DESC
            ),

            -- Уменьшить количество в товаре с дефектом

            wup1 as (

                update
                    goods_acceptance_item up1
                set
                    initial_quantity = up1.initial_quantity - pp1.rm_quantity,
                    quantity = case
                        when up1.quantity > pp1.rm_quantity
                        then up1.quantity - pp1.rm_quantity
                        else up1.initial_quantity - pp1.rm_quantity
                        end
                from (
                    select
                        id,
                        rm_quantity
                    from
                        proporcional
                    where
                        rm_quantity < initial_quantity) pp1
                left join goods_acceptance_item old_up1 on old_up1.id = pp1.id
                where
                    up1.id = pp1.id
                    and 0 = (select min(rest) from proporcional)

                returning
                    pp1.id as item_id,
                    0 as quantity

            ),

            -- Увеличить количество в товаре без дефекта

            wup2 as (

                update
                    goods_acceptance_item up2
                set
                    quantity = up2.quantity + pp2.rm_quantity,
                    initial_quantity = up2.initial_quantity + pp2.rm_quantity
                from (
                    select
                        ai2.id,
                        p2.rm_quantity
                    from
                        accept_items ai2
                    inner join proporcional p2 on
                        p2.order_item_id = ai2.order_item_id) pp2
                left join goods_acceptance_item old_up2 on old_up2.id = pp2.id
                where
                    up2.id = pp2.id
                    and 0 = (select min(rest) from proporcional)

                returning
                    pp2.id as item_id,
                    pp2.rm_quantity as quantity

            ),

            -- Создание новых записей без дефекта

            win1 as (

                insert into goods_acceptance_item as in1 (
                    goods_acceptance_did,
                    base_product_id,
                    goods_pallet_id,
                    quantity,
                    initial_quantity,
                    order_item_id,
                    supply_item_id,
                    goods_state_code)
                select
                    {$command->goodsAcceptanceId},
                    {$command->id},
                    null,
                    p3.rm_quantity,
                    p3.rm_quantity,
                    p3.order_item_id,
                    p3.supply_item_id,
                    'normal'::goods_state_code
                from proporcional p3
                left join accept_items ai3 on
                    ai3.order_item_id = p3.order_item_id
                where
                    ai3.order_item_id is null
                    and 0 = (select min(rest) from proporcional)

                returning
                    id as item_id,
                    quantity as quantity

            ),

            -- Удалить полностью востанновленные строки

            wdl1 as (

                delete from
                    goods_acceptance_item dl1
                where
                    dl1.id in (
                        select
                            id
                        from
                            proporcional
                        where
                            rm_quantity = initial_quantity)
                    and 0 = (select min(rest) from proporcional)

                returning
                    dl1.id as item_id,
                    0 as quantity

            )

            select 
                res.item_id,
                sum(res.quantity) as quantity
            from (
                select * from wup1
                union all
                select * from wup2
                union all
                select * from win1
                union all
                select * from wdl1
            ) res
            group by
                res.item_id
        ";


        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('item_id', 'itemId', 'integer');
        $rsm->addScalarResult('quantity', 'quantity', 'integer');

        $items = $em->createNativeQuery($queryText, $rsm)->getArrayResult();
        
        if (0 == count($items)) {
            throw new BadRequestHttpException('Нет товара для отмены отметки несоответствующего качества.');
        }
        
        // Сбор направлений отгрузки по результатам выполнения маркировки
        
        $queryText = "
            with

            -- Данные, обработанные предыдущим запросом
            real_items (item_id, quantity) as(
        ";
        
        $firstTurn = true;
        foreach ($items as $item) {
            
            if (0 == $item['quantity']) continue;
            
            if ($firstTurn) {
                $firstTurn = !$firstTurn;
                
                $queryText .= "
                    select
                        {$item['itemId']} as item_id,
                        {$item['quantity']} as quantity
                ";
                        
            }else{
                
                $queryText .= "
                    
                    union all
                    
                    select
                        {$item['itemId']},
                        {$item['quantity']}
                ";
                        
            }
        }
        
        $queryText .= "
            )
        
            select
                o.geo_point_id,
                gp.code,
                gp.name,
                sum(ri.quantity) as quantity
            from real_items ri
            left join goods_acceptance_item gai on ri.item_id = gai.id
            -- заказ и направление
            left join order_item oi on oi.id = gai.order_item_id
            left join \"order\" o on o.id = oi.order_id
            left join geo_point gp
            on o.geo_point_id = gp.id
            
            group by
                o.geo_point_id,
                gp.code,
                gp.name
        ";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('geo_point_id', 'geoPointId', 'integer');
        $rsm->addScalarResult('code', 'geoPointCode', 'string');
        $rsm->addScalarResult('name', 'quaPointName', 'string');
        $rsm->addScalarResult('quantity', 'quantity', 'integer');

        $items = $em->createNativeQuery($queryText, $rsm)->getResult();
        
        return $items;

    }
}