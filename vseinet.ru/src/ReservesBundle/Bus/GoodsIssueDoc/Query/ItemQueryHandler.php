<?php 

namespace ReservesBundle\Bus\GoodsIssueDoc\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use AppBundle\ORM\Query\DTORSM;

class ItemQueryHandler extends MessageHandler
{
    public function handle(ItemQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $queryText = "
            select
                i.did as id,
                i.number,
                case when i.parent_doc_did is null then
                    null
                else
                    json_build_object(
                        'id', parent_ad.did, 
                        'title', parent_ad.title, 
                        'document_type', pg_class.relname)
                end as parent_doc,
                i.created_at as created_at,
                i.created_by as created_by,
                concat(
                    case when pc.firstname is null  then '' else concat(pc.firstname,
                        case when pc.lastname is null and pc.secondname is null then '' else ' ' end) end,
                    case when pc.secondname is null then '' else concat(pc.secondname,
                        case when pc.lastname is null   then '' else ' ' end) end,
                    case when pc.lastname is null   then '' else pc.lastname end) as created_name,
                i.completed_at as completed_at,
                i.completed_by as completed_by,
                concat(
                    case when pa.firstname is null  then '' else concat(pa.firstname,
                        case when pa.lastname is null and pa.secondname is null then '' else ' ' end) end,
                    case when pa.secondname is null then '' else concat(pa.secondname,
                        case when pa.lastname is null   then '' else ' ' end) end,
                    case when pa.lastname is null   then '' else pa.lastname end) as completed_name,
                i.registered_at as registered_at,
                i.registered_by as registered_by,
                concat(
                    case when pr.firstname is null  then '' else concat(pr.firstname,
                        case when pr.lastname is null and pr.secondname is null then '' else ' ' end) end,
                    case when pr.secondname is null then '' else concat(pr.secondname,
                        case when pr.lastname is null   then '' else ' ' end) end,
                    case when pr.lastname is null   then '' else pr.lastname end) as registered_name,
                i.title,
                i.goods_issue_doc_type_id as goods_issue_doc_type_id,
                git.name as goods_issue_doc_type_name,
                i.status_code as status_code,
                case when i.geo_room_id is null then
                    null
                else
                    json_build_object(
                        'id', i.geo_room_id, 
                        'name', concat(geo_c.name, ', ', geo_p.name, ', ', geo_r.name))
                end as geo_room,
                --
                i.base_product_id,
                bp.name as base_product_name,
                i.goods_state_code,
                i.quantity,
                ord.number AS order_number,
                i.order_item_id,
                case
                    when ord.title is not null then ord.title
                    else 'Свободный остаток'
                    end as order_title,
                i.supply_item_id,
                case
                    when sui_su.title is not null then sui_su.title
                    when sui_gi.title is not null then sui_gi.title
                    when sui_gp.title is not null then sui_gp.title
                    else 'Партия не определена'
                    end as supply_title,
                sui_su.number AS supply_number,
                agir.sum_goods as sum_goods,
                agir.sum_client as sum_client,
                agir.sum_supplier as sum_supplier,
                -- -----------------------------
                i.description,
                i.product_condition,
                CASE WHEN i.supplier_id > 0 THEN i.supplier_id ELSE sui_su.supplier_id END,
                s.code as supplier_code,
                sui.purchase_price as purchase_price,
                oi.retail_price as retail_price

            from goods_issue_doc i

            left join \"goods_issue_doc_type\" git on i.goods_issue_doc_type_id = git.id
            
            -- наименование товара
            inner join base_product bp on
                bp.id = i.base_product_id

            -- заголовок заказа
            left join order_item as ori on
                ori.id = i.order_item_id
            left join order_doc as ord on
                ord.number = ori.order_id

            -- заголовок партии
            inner join supply_item as sui on
                sui.id = i.supply_item_id
            left join supply_doc as sui_su on
                sui.parent_doc_type = 'supply'::document_type_code and sui_su.number = sui.parent_doc_id
            left join goods_issue_doc as sui_gi on
                sui.parent_doc_type = 'goods_issue'::document_type_code and sui_gi.number = sui.parent_doc_id
            left join goods_packaging as sui_gp on
                sui.parent_doc_type = 'goods_packaging'::document_type_code and sui_gp.number = sui.parent_doc_id

            -- Наименование склада
            left join geo_room geo_r on geo_r.id = i.geo_room_id
            left join geo_point geo_p on geo_p.id = geo_r.geo_point_id
            left join geo_city geo_c on geo_c.id = geo_p.geo_city_id
            
            -- документ-основние
            left join any_doc* parent_ad on parent_ad.did = i.parent_doc_did
            left join pg_class pg_class on pg_class.oid = parent_ad.tableoid
            
            -- автор документа
            left join \"user\" uc on i.created_by = uc.id
            left join \"person\" pc on uc.person_id = pc.id
            
            -- кто закрыл документ
            left join \"user\" ua on i.completed_by = ua.id
            left join \"person\" pa on ua.person_id = pa.id

            -- кто провёл документ
            left join \"user\" ur on i.registered_by = ur.id
            left join \"person\" pr on ur.person_id = pr.id

            -- остатки по претензии
            left join (
                select
                    gir.goods_issue_doc_did,
                    sum(gir.delta_goods) as sum_goods,
                    sum(gir.delta_client) as sum_client,
                    sum(gir.delta_supplier) as sum_supplier
                from \"goods_issue_register\" gir
                group by gir.goods_issue_doc_did
            ) agir on i.did = agir.goods_issue_doc_did
            
            -- продажа
            left join \"client_order_item\" oi on i.order_item_id = oi.order_item_id

            left join \"supplier\" s on s.id = sui_su.supplier_id
            
            where i.did = {$query->id}
            ";

        $items = $em->createNativeQuery($queryText, new DTORSM(DTO\DocumentHead::class, DTORSM::ARRAY_INDEX))
                ->getResult('DTOHydrator');

        if (empty($items)) { throw new NotFoundHttpException('Документ не найден'); }
        
        return $items[0];
        
    }

}
