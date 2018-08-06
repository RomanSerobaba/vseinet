<?php

namespace ReservesBundle\Bus\GoodsIssueDoc\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\Bus\GoodsIssueDoc\Query\DTO\DocumentList;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use AppBundle\ORM\Query\DTORSM;

class ListRelatedElementsQueryHandler extends MessageHandler
{
    public function handle(ListRelatedElementsQuery $query)
    {

        $queryText = "
            select
            
                rel.id,
                rel.type,
                rel.created_at,
                rel.created_by,
                rel.created_name,
                rel.completed_at,
                rel.completed_by,
                rel.completed_name,
                rel.registered_at,
                rel.registered_by,
                rel.registered_name,
                rel.title,
                rel.sum_goods,
                rel.sum_client,
                rel.sum_supplier
            
            from (
            
                -- Выбор комментариев

                select

                    dc.id,
                    'comment' as \"type\",
                    dc.created_at,
                    dc.created_by,
                    concat(
                        case when pc.firstname is null  then '' else concat(pc.firstname,
                            case when pc.lastname is null and pc.secondname is null then '' else ' ' end) end,
                        case when pc.secondname is null then '' else concat(pc.secondname,
                            case when pc.lastname is null   then '' else ' ' end) end,
                        case when pc.lastname is null   then '' else pc.lastname end) as created_name,
                    null as completed_at,
                    null as completed_by,
                    null as completed_name,
                    null as registered_at,
                    null as registered_by,
                    null as registered_name,
                    dc.comment as title,
                    null as sum_goods,
                    null as sum_client,
                    null as sum_supplier

                from any_doc_comment as dc

                -- наименование автора
                left join \"user\" uc on dc.created_by = uc.id  
                left join person pc on uc.person_id = pc.id

                where dc.any_doc_did = {$query->id}

                union all

                -- Выбор докмуентов
                select
                    ad.did as id,
                    pg_class.relname as \"type\",
                    ad.created_at as created_at,
                    ad.created_by as created_by,
                    concat(
                        case when pc.firstname is null  then '' else concat(pc.firstname,
                            case when pc.lastname is null and pc.secondname is null then '' else ' ' end) end,
                        case when pc.secondname is null then '' else concat(pc.secondname,
                            case when pc.lastname is null   then '' else ' ' end) end,
                        case when pc.lastname is null   then '' else pc.lastname end) as created_name,
                    ad.completed_at as completed_at,
                    ad.completed_by as completed_by,
                    concat(
                        case when pa.firstname is null  then '' else concat(pa.firstname,
                            case when pa.lastname is null and pa.secondname is null then '' else ' ' end) end,
                        case when pa.secondname is null then '' else concat(pa.secondname,
                            case when pa.lastname is null   then '' else ' ' end) end,
                        case when pa.lastname is null   then '' else pa.lastname end) as completed_name,
                    ad.registered_at as registered_at,
                    ad.registered_by as registered_by,
                    concat(
                        case when pr.firstname is null  then '' else concat(pr.firstname,
                            case when pr.lastname is null and pr.secondname is null then '' else ' ' end) end,
                        case when pr.secondname is null then '' else concat(pr.secondname,
                            case when pr.lastname is null   then '' else ' ' end) end,
                        case when pr.lastname is null   then '' else pr.lastname end) as registered_name,
                    ad.title,
                    -agir.sum_goods as sum_goods,
                    -agir.sum_client as sum_client,
                    -agir.sum_supplier as sum_supplier

                from any_doc* ad

                -- тип документа
                left join pg_catalog.pg_class pg_class on pg_class.oid = ad.tableoid

                -- автор документа
                left join \"user\" uc on ad.created_by = uc.id
                left join \"person\" pc on uc.person_id = pc.id

                -- кто закрыл документ
                left join \"user\" ua on ad.completed_by = ua.id
                left join \"person\" pa on ua.person_id = pa.id

                -- кто провёл документ
                left join \"user\" ur on ad.registered_by = ur.id
                left join \"person\" pr on ur.person_id = pr.id

                -- остатки по претензии
                left join (
                    select
                        gir.goods_issue_doc_did,
                        sum(gir.delta_goods) as sum_goods,
                        sum(gir.delta_client) as sum_client,
                        sum(gir.delta_supplier) as sum_supplier
                    from goods_issue_register gir
                    group by gir.goods_issue_doc_did
                ) agir on ad.did = agir.goods_issue_doc_did

                where ad.parent_doc_did = {$query->id}
            ) rel    
            
            order by rel.created_at, rel.type, rel.id
        ";

        $relatedElements = $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, new DTORSM(DTO\DocumentRelatedElement::class, DTORSM::ARRAY_INDEX))
                ->getResult('DTOHydrator');

        return $relatedElements;
    }

}