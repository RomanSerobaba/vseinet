<?php

namespace ReservesBundle\Bus\GoodsIssueDoc\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\Bus\GoodsIssueDoc\Query\DTO\DocumentList;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use AppBundle\ORM\Query\DTORSM;

class ListQueryHandler extends MessageHandler
{
    public function handle(ListQuery $query)
    {

        $setParameters = [];

        $queryCore = "

            from goods_issue_doc i

            left join \"goods_issue_doc_type\" git on i.goods_issue_doc_type_id = git.id";


        $queryCount = "
            select count(*) as cnt". $queryCore;


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
                i.base_product_id,
                bp.name as base_product_name,
                agir.sum_goods as sum_goods,
                agir.sum_client as sum_client,
                agir.sum_supplier as sum_supplier,
                si.purchase_price as purchase_price,
                oi.retail_price as retail_price
            ". $queryCore ."
                
            -- наименование товара
            inner join base_product bp on bp.id = i.base_product_id

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

            -- закупка
            left join \"supply_item\" si on i.supply_item_id = si.id
            
            -- продажа
            left join \"client_order_item\" oi on i.order_item_id = oi.order_item_id

            ";

        // Просмотр архива

        if (!$query->withCompleted) {

            $queryText .= "
                where i.completed_at is null";

            $queryCount .= "
                where i.completed_at is null";

        }else{

            $queryText .= "
                where 1 = 1";

            $queryCount .= "
                where 1 = 1";

        }

        if (!empty($query->inStatuses)) {

            $queryText .= "
                and i.status_code in (:inStatuses)";

            $queryCount .= "
                and i.status_code in (:inStatuses)";

            $setParameters['inStatuses'] = $query->inStatuses;

        }

        if (!empty($query->inGoodsIssueDocTypeIds)) {

            $queryText .= "
                and i.goods_issue_doc_type_id in (:inGoodsIssueDocTypeIds)";

            $queryCount .= "
                and i.goods_issue_doc_type_id in (:inGoodsIssueDocTypeIds)";

            $setParameters['inGoodsIssueDocTypeIds'] = $query->inGoodsIssueDocTypeIds;

        }

        if (!empty($query->inGeoRoomsIds)) {

            $queryText .= "
                and i.geo_room_id in (:inGeoRoomsIds)";

            $queryCount .= "
                and i.geo_room_id in (:inGeoRoomsIds)";

            $setParameters['inGeoRoomsIds'] = $query->inGeoRoomsIds;

        }

        if (!empty($query->inCreatedBy)) {

            $queryText .= "
                and i.created_by in (:inCreatedBy)";

            $queryCount .= "
                and i.created_by in (:inCreatedBy)";

            $setParameters['inCreatedBy'] = $query->inCreatedBy;

        }

        //////////// Общее управление списком

        // Интервал дат

        if ($query->fromDate) {
            $queryText .= "
                and i.created_at >= :fromDate";
            $queryCount .= "
                and i.created_at >= :fromDate";
            $setParameters['fromDate'] = $query->fromDate.'T00:00:00';
        }
        if ($query->toDate) {

            $queryText .= "
                and i.created_at <= :toDate";
            $queryCount .= "
                and i.created_at <= :toDate";
            $setParameters['toDate'] = $query->toDate.'T23:59:59';
        }

        $queryText .= "
            order by i.created_at desc, i.did desc";

        // Пагинация

        if ($query->limit) {
            $queryText .= "
                limit {$query->limit}";
        }
        if ($query->page) {
            $offset = ($query->page - 1) * $query->limit;
            $queryText .= " offset {$offset}";
        }

        ////

        $em = $this->getDoctrine()->getManager();

        $dbQuery = $em->createNativeQuery($queryText, new DTORSM(DTO\Documents::class, DTORSM::ARRAY_INDEX))
                ->setParameters($setParameters);
        //echo $dbQuery->getSQL(); die();
        $documents = $dbQuery->getResult('DTOHydrator');

        $dbCount = $em->createNativeQuery($queryCount, new ResultSetMapping())
                ->setParameters($setParameters);
        $counters = $dbCount->getResult('ListHydrator');

//        $products = [];
//
//        if (!empty($documents)) {
//            foreach ($documents as $document) {
//                foreach ($document->baseProductsIds as $product) {
//                    if (!in_array($product, $products)) {
//                        $products[] = $product;
//                    }
//                }
//            }
//        }

        if (!empty($products)) {
            
            $productsFilrter = implode(',', $products);
            
            $queryText = "
                select
                    id, name
                from base_product
                where id in ({$productsFilrter})";

            $dbQuery= $em->createNativeQuery($queryText, new DTORSM(DTO\SimpleData::class, DTORSM::ARRAY_INDEX));
            $products = $dbQuery->getResult('DTOHydrator');
                
        }

        return new DTO\DocumentList($documents, $counters[0]);
    }

}