<?php 

namespace FinanseBundle\Bus\FinancialOperations\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;
use Doctrine\ORM\Query\ResultSetMapping;

class ListQueryHandler extends MessageHandler
{
    public function handle(ListQuery $query)
    {

        $setParameters = [];

        $queryCount = "
            select
            
                count(did) as cnt
            
            from any_doc* i
            
            -- тип документа
            left join pg_class pgc on pgc.oid = i.tableoid
            ";


        $queryList = "
            select
                -- общие поля дкументов
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
                i.status_code as status_code,
                -- персональные поля документа
                pgc.relname as document_type,
                null as city_id,
                null as city_name,
                case pgc.relname
                    when 'bank_operation_doc' then bod.amount
                    when 'financial_operation_doc' then fod.amount
                    end as total_amount,
                case pgc.relname
                    when 'bank_operation_doc' then bod.financial_resource_id
                    when 'financial_operation_doc' then fod.financial_resource_id
                    end as financial_resources_id,
                case pgc.relname
                    when 'bank_operation_doc' then bod_fr.title
                    when 'financial_operation_doc' then fod_fr.title
                    end as financial_resources_name,
                case pgc.relname
                    when 'bank_operation_doc' then 
                        array_to_json(array(
                            select json_build_object(
                                'payCode', 'bank', 
                                'amount', bod.amount)))
                    when 'financial_operation_doc' then
                        array_to_json(array(
                            select json_build_object(
                                'payCode', 'bank', 
                                'amount', fod.amount)))
                    end as amounts_by_codes,
                case pgc.relname
                    when 'bank_operation_doc' then 
                        array_to_json(array(
                            select
                                json_build_object(
                                    'id', bod_rd.related_document_did, 
                                    'title', ad_pgc.title, 
                                    'document_type', bod_pgc.relname)
                            from bank_operation_doc_related_document bod_rd
                            inner join any_doc ad_pgc on ad_pgc.did = bod.did
                            inner join pg_class bod_pgc on bod_pgc.oid = bod.tableoid
                            where bod_rd.bank_operation_doc_did = bod.did
                            ))
                    when 'financial_operation_doc' then
                        array_to_json(array(
                            select
                                json_build_object(
                                    'id', fod_rd.related_document_did, 
                                    'title', ad_pgc.title, 
                                    'document_type', fod_pgc.relname)
                            from financial_operation_doc_related_document fod_rd
                            inner join any_doc ad_pgc on ad_pgc.did = fod.did
                            inner join pg_class fod_pgc on fod_pgc.oid = fod.tableoid
                            where fod_rd.financial_operation_doc_did = fod.did
                            ))
                    end as related_documents
                
            from any_doc* i
                
            -- тип документа
            left join pg_class pgc on pgc.oid = i.tableoid
            
            -- Персональные аттрибуты банковского документа
            left join bank_operation_doc bod on bod.did = i.did
            left join financial_resource bod_fr on bod_fr.id = bod.financial_resource_id
            
            -- Персональные аттрибуты внутреннего документа
            left join financial_operation_doc fod on fod.did = i.did
            left join financial_resource fod_fr on fod_fr.id = fod.financial_resource_id
            
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
            
            ";

        $queryList .= "
            where
                pgc.relname in ('bank_operation_doc', 'financial_operation_doc')";

        $queryCount .= "
            where
                pgc.relname in ('bank_operation_doc', 'financial_operation_doc')";
        
        // Просмотр архива

        if (!$query->withCompleted) {

            $queryList .= "
                and i.completed_at is null";

            $queryCount .= "
                and i.completed_at is null";

        }

        if (!empty($query->inStatuses)) {

            $queryList .= "
                and i.status_code in (:inStatuses)";

            $queryCount .= "
                and i.status_code in (:inStatuses)";

            $setParameters['inStatuses'] = $query->inStatuses;

        }

        if (!empty($query->inGeoCitiesIds)) {

//            $queryList .= "
//                and i.geo_city_id in (:inGeoCitiesIds)";
//
//            $queryCount .= "
//                and i.geo_city_id in (:inGeoCitiesIds)";
            
            $queryList .= "
                and 1 in (:inGeoCitiesIds)";

            $queryCount .= "
                and 1 in (:inGeoCitiesIds)";

            $setParameters['inGeoCitiesIds'] = $query->inGeoCitiesIds;

        }

        if (!empty($query->inRepresentativesIds)) {

//            $queryList .= "
//                and i.representative_id in (:inRepresentativesIds)";
//
//            $queryCount .= "
//                and i.representative_id in (:inRepresentativesIds)";

            $queryList .= "
                and 141 in (:inRepresentativesIds)";

            $queryCount .= "
                and 141 in (:inRepresentativesIds)";

            $setParameters['inRepresentativesIds'] = $query->inRepresentativesIds;

        }

        if (!empty($query->inCreatedBy)) {

            $queryList .= "
                and i.created_by in (:inCreatedBy)";

            $queryCount .= "
                and i.created_by in (:inCreatedBy)";

            $setParameters['inCreatedBy'] = $query->inCreatedBy;

        }

        //////////// Общее управление списком

        // Интервал дат

        if ($query->fromDate) {
            $queryList .= "
                and i.created_at >= :fromDate";
            $queryCount .= "
                and i.created_at >= :fromDate";
            $setParameters['fromDate'] = $query->fromDate.'T00:00:00';
        }
        if ($query->toDate) {

            $queryList .= "
                and i.created_at <= :toDate";
            $queryCount .= "
                and i.created_at <= :toDate";
            $setParameters['toDate'] = $query->toDate.'T23:59:59';
        }

        $queryList .= "
            order by i.created_at desc, i.did desc";

        // Пагинация

        if ($query->limit) {
            $queryList .= "
                limit {$query->limit}";
        }
        if ($query->page) {
            $offset = ($query->page - 1) * $query->limit;
            $queryList .= " offset {$offset}";
        }

        ////

        $em = $this->getDoctrine()->getManager();

        $documents = $em->createNativeQuery($queryList, new DTORSM(DTO\Documents::class, DTORSM::ARRAY_INDEX))
                ->setParameters($setParameters)
                ->getResult('DTOHydrator');

        $counters = $em->createNativeQuery($queryCount, new ResultSetMapping())
                ->setParameters($setParameters)
                ->getResult('ListHydrator');

        return new DTO\DocumentList($documents, $counters[0]);

    }

}