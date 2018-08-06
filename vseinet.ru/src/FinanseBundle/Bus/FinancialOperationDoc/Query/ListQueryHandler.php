<?php 

namespace FinanseBundle\Bus\FinancialOperationDoc\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use FinanseBundle\Bus\FinancialOperationDoc\Query\DTO\DocumentList;
use Doctrine\ORM\Query\ResultSetMapping;
use AppBundle\ORM\Query\DTORSM;

class ListQueryHandler extends MessageHandler
{
    public function handle(ListQuery $query)
    {


        $setParameters = [];
        $em = $this->getDoctrine()->getManager();

        // Запрос-счетчик

        $queryCount = "
            select
                count(did) as cnt
            from financial_operation_doc i
            ";

        // Запрос-список

        $queryList = "
            select
                -- общие поля дкументов
                i.did as id,
                i.number as number,
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
                i.title as title,
                i.status_code as status_code
                -- персональные поля документа
                
            from financial_operation_doc i
                
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

        // Просмотр архива

        if (!$query->withCompleted) {

            $queryList .= "
                where i.completed_at is null";

            $queryCount .= "
                where i.completed_at is null";

        }else{

            $queryList .= "
                where 1 = 1";

            $queryCount .= "
                where 1 = 1";

        }

        // Фильтр по статусу

        if (!empty($query->inStatuses)) {

            $queryList .= "
                and i.status_code in (:inStatuses)";

            $queryCount .= "
                and i.status_code in (:inStatuses)";

            $setParameters['inStatuses'] = $query->inStatuses;

        }

        // Фильтр по автору

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

        /////////////////////////////////////////////////////////////
        //
        //  Тут идет подсчет общего числа элементов списка,
        //  дальше обрабатывается только запрос списка
        //
                
        $totoal = $em->createNativeQuery($queryCount, new ResultSetMapping())
                ->setParameters($setParameters)
                ->getSingleScalarResult();
                        
        /////////////////////////////////////////////////////////////
        
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

        return new DTO\DocumentList(
                $em->createNativeQuery($queryList, new DTORSM(DTO\Documents::class, DTORSM::ARRAY_INDEX))                
                ->setParameters($setParameters)
                ->getResult('DTOHydrator'), $totoal);

    }

}