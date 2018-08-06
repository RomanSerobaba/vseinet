<?php

namespace FinanseBundle\Bus\ExpenseOperations\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;
use Doctrine\ORM\Query\ResultSetMapping;
use DocumentBundle\SimpleTools\DocumentQueryHelper;
use DocumentBundle\SimpleTools\DocumentNameConverter;

class ListQueryHandler extends MessageHandler
{
    public function handle(ListQuery $query)
    {

        $setParameters = [];

        $inDocumentType = [
            'expense_simple_doc',
            'accountable_expenses_doc',
            'supplier_order_expenses_doc',
            'buyer_order_expenses_doc'];

        if (!empty($query->inDocumentType)) {

            $inDocumentType = [];
            foreach ($query->inDocumentType as $strType) {
                $inDocumentType[] = DocumentNameConverter::Type2TableName($strType);
            }

        }

        $setParameters['inDocumentType'] = $inDocumentType;

        $queryCount = "
            select

                count(did) as cnt

            from any_doc* i

            -- тип документа
            left join pg_class pgc on pgc.oid = i.tableoid
            ";


        $queryList = "
            select" . DocumentQueryHelper::buildSelect('i') .",
                -- персональные поля документа

                pgc.relname as document_type,

                case pgc.relname
                    when 'expense_simple_doc'          then esd.amount
                    when 'accountable_expenses_doc'    then aed.amount
                    when 'buyer_order_expenses_doc'    then boed.amount
                    when 'supplier_order_expenses_doc' then soed.amount
                    else null
                    end as amount,

                case pgc.relname
                    when 'expense_simple_doc'          then esd.financial_resource_id
                    when 'accountable_expenses_doc'    then aed.financial_resource_id
                    when 'buyer_order_expenses_doc'    then null
                    when 'supplier_order_expenses_doc' then soed.financial_resource_id
                    else null
                    end as financial_resources_id,

                case pgc.relname
                    when 'expense_simple_doc'          then esd_fr.title
                    when 'accountable_expenses_doc'    then aed_fr.title
                    when 'buyer_order_expenses_doc'    then null
                    when 'supplier_order_expenses_doc' then soed_fr.title
                    else null
                    end as financial_resources_name,

                case pgc.relname
                    when 'accountable_expenses_doc'    then aed.financial_counteragent_id
                    when 'buyer_order_expenses_doc'    then boed.financial_counteragent_id
                    when 'supplier_order_expenses_doc' then soed.financial_counteragent_id
                    else null
                    end as financial_counteragent_id,

                case pgc.relname
                    when 'accountable_expenses_doc'    then aed_fc.name
                    when 'buyer_order_expenses_doc'    then boed_fc.name
                    when 'supplier_order_expenses_doc' then soed_fc.name
                    else null
                    end as financial_counteragent_name,

                case pgc.relname
                    when 'expense_simple_doc' then esd.equipment_id
                    else null
                    end as equipment_id,

                case pgc.relname
                    when 'expense_simple_doc' then
                        case
                            when esd_eq.id is null then null
                            when esd_eq.reg_number is not null then concat(esd_eq.name, ' (', esd_eq.reg_number,')')
                            else esd_eq.name end
                    else null
                    end as equipment_name,

                case pgc.relname
                    when 'expense_simple_doc'          then esd.org_department_id
                    when 'accountable_expenses_doc'    then aed.org_department_id
                    when 'buyer_order_expenses_doc'    then boed.org_department_id
                    when 'supplier_order_expenses_doc' then soed.org_department_id
                    else null
                    end as org_department_id,

                case pgc.relname
                    when 'expense_simple_doc'          then esd_gp.name
                    when 'accountable_expenses_doc'    then aed_gp.name
                    when 'buyer_order_expenses_doc'    then boed_gp.name
                    when 'supplier_order_expenses_doc' then soed_gp.name
                    else null
                    end as org_department_name,

                case pgc.relname
                    when 'expense_simple_doc'          then esd.item_of_expenses_id
                    when 'accountable_expenses_doc'    then aed.to_item_of_expenses_id
                    when 'buyer_order_expenses_doc'    then boed.to_item_of_expenses_id
                    when 'supplier_order_expenses_doc' then soed.item_of_expenses_id
                    else null
                    end as item_of_expenses_id,

                case pgc.relname
                    when 'expense_simple_doc'          then esd_ioe.name
                    when 'accountable_expenses_doc'    then aed_ioe.name
                    when 'buyer_order_expenses_doc'    then boed_ioe.name
                    when 'supplier_order_expenses_doc' then soed_ioe.name
                    else null
                    end as item_of_expenses_name,

                case pgc.relname
                    when 'expense_simple_doc'          then esd.expected_date_execute
                    when 'accountable_expenses_doc'    then aed.expected_date_execute
                    when 'buyer_order_expenses_doc'    then boed.expected_date_execute
                    when 'supplier_order_expenses_doc' then soed.expected_date_execute
                    else null
                    end as expected_date_execute,

                case pgc.relname
                    when 'accountable_expenses_doc' then aed.maturity_date_execute
                    when 'buyer_order_expenses_doc' then boed.maturity_date_execute
                    else null
                    end as maturity_date_execute,

                case pgc.relname
                    when 'expense_simple_doc'          then esd.description
                    when 'accountable_expenses_doc'    then aed.description
                    when 'buyer_order_expenses_doc'    then boed.description
                    when 'supplier_order_expenses_doc' then soed.description
                    else null
                    end as description

            from any_doc* i" . DocumentQueryHelper::buildJoin('i') ."

            -- тип документа
            inner join pg_class pgc on pgc.oid = i.tableoid

            -- Простой расход
            left join expense_simple_doc esd     on esd.did = i.did
            left join item_of_expenses   esd_ioe on esd_ioe.id = esd.item_of_expenses_id
            left join org_department     esd_gp  on esd_gp.id = esd.org_department_id
            left join financial_resource esd_fr  on esd_fr.id = esd.financial_resource_id
            left join equipment          esd_eq  on esd_eq.id = esd.equipment_id

            -- Выдача под отчет
            left join accountable_expenses_doc aed     on aed.did = i.did
            left join item_of_expenses         aed_ioe on aed_ioe.id = aed.to_item_of_expenses_id
            left join org_department           aed_gp  on aed_gp.id = aed.org_department_id
            left join financial_resource       aed_fr  on aed_fr.id = aed.financial_resource_id
            left join financial_counteragent   aed_fc  on aed_fc.id = aed.financial_counteragent_id

            -- Выставленные счета
            left join buyer_order_expenses_doc boed     on boed.did = i.did
            left join item_of_expenses         boed_ioe on boed_ioe.id = boed.to_item_of_expenses_id
            left join org_department           boed_gp  on boed_gp.id = boed.org_department_id
            left join financial_resource       boed_fr  on boed_fr.id = boed.to_financial_resource_id
            left join financial_counteragent   boed_fc  on boed_fc.id = boed.financial_counteragent_id

            -- Оплата полученых счетов
            left join supplier_order_expenses_doc soed     on soed.did = i.did
            left join item_of_expenses            soed_ioe on soed_ioe.id = soed.item_of_expenses_id
            left join org_department              soed_gp  on soed_gp.id = soed.org_department_id
            left join financial_resource          soed_fr  on soed_fr.id = soed.financial_resource_id
            left join financial_counteragent      soed_fc  on soed_fc.id = soed.financial_counteragent_id

            ";

        $queryList .= "
            where
                pgc.relname in (:inDocumentType)";

        $queryCount .= "
            where
                pgc.relname in (:inDocumentType)";

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