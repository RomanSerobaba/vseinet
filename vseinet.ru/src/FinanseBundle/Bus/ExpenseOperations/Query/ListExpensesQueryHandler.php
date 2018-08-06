<?php

namespace FinanseBundle\Bus\ExpenseOperations\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;
use Doctrine\ORM\Query\ResultSetMapping;
use DocumentBundle\SimpleTools\DocumentQueryHelper;
use DocumentBundle\SimpleTools\DocumentNameConverter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ListExpensesQueryHandler extends MessageHandler
{

    public function handle(ListExpensesQuery $query)
    {

        // Пагинация

        $queryLimit = "
            limit {$query->limit}";

        if (!empty($query->page)) {
            $offset = ($query->page - 1) * $query->limit;
            $queryLimit .= " offset {$offset}";
        }

        ////

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

        if (!empty($query->fromDate))
            $setParameters['fromDate'] = $query->fromDate . 'T00:00:00';

        if (!empty($query->toDate))
            $setParameters['toDate'] = $query->toDate . 'T23:59:59';

        if (!empty($query->inOrgDepartmentIds))
            $setParameters['inOrgDepartmentIds'] = $query->inOrgDepartmentIds;

        if (!empty($query->inStatuses))
            $setParameters['inStatuses'] = $query->inStatuses;

        if (!empty($query->inCreatedBy))
            $setParameters['inCreatedBy'] = $query->inCreatedBy;

        if (!empty($query->inFinancialResourcesIds))
            $setParameters['inFinancialResourcesIds'] = $query->inFinancialResourcesIds;

        if (!empty($query->inFinancialCounteragentsIds))
            $setParameters['inFinancialCounteragentsIds'] = $query->inFinancialCounteragentsIds;

        if (!empty($query->inEquipmentsIds))
            $setParameters['inEquipmentsIds'] = $query->inEquipmentsIds;

        if (!empty($query->inItemsOfExpensesIds))
            $setParameters['inItemsOfExpensesIds'] = $query->inItemsOfExpensesIds;

        // Построение CTE запроса

        $queryList = "
        with";

        $queryCount = $queryList;

        $cnt = 0;
        foreach ($inDocumentType as $documentType) {

            $alias = "i" . $cnt;

            $queryCount .= "
            cte_{$documentType} as (
                select
                    1
                from \"{$documentType}\" {$alias}
                where 1 = 1";

            $queryList .= "
            cte_{$documentType} as (
                select" . DocumentQueryHelper::buildSelect("{$alias}") . ",
                    -- общие поля расходов
                    '{$documentType}' as document_type,
                    {$alias}.org_department_id,
                    {$alias}.amount,
                    {$alias}.expected_date_execute,
                    {$alias}.description,
                    {$alias}.accepted_at,
                    {$alias}.accepted_by,
                    {$alias}.rejected_at,
                    {$alias}.rejected_by,";

            if ('expense_simple_doc' == $documentType) {
                $queryList .= "
                    -- expense_simple_doc
                    {$alias}.financial_resource_id,
                    {$alias}.item_of_expenses_id,
                    {$alias}.equipment_id,
                    null::integer as financial_counteragent_id,
                    null::integer as to_item_of_expenses_id,
                    null::integer as to_equipment_id,
                    null::timestamp as maturity_date_execute,
                    0 as amount_bonus,
                    0 as amount_mutual,
                    null::integer as to_financial_resource_id
                    ";
            }

            if ('accountable_expenses_doc' == $documentType) {
                $queryList .= "
                    -- accountable_expenses_doc
                    {$alias}.financial_resource_id,
                    null::integer as item_of_expenses_id,
                    null::integer as equipment_id,
                    {$alias}.financial_counteragent_id,
                    {$alias}.to_item_of_expenses_id,
                    {$alias}.to_equipment_id,
                    {$alias}.maturity_date_execute,
                    0 as amount_bonus,
                    0 as amount_mutual,
                    null::integer as to_financial_resource_id
                    ";
            }

            if ('supplier_order_expenses_doc' == $documentType) {
                $queryList .= "
                    -- supplier_order_expenses_doc
                    {$alias}.financial_resource_id,
                    {$alias}.item_of_expenses_id,
                    null::integer as equipment_id,
                    {$alias}.financial_counteragent_id,
                    null::integer as to_item_of_expenses_id,
                    null::integer as to_equipment_id,
                    null::timestamp as maturity_date_execute,
                    {$alias}.amount_bonus,
                    {$alias}.amount_mutual,
                    null::integer as to_financial_resource_id
                    ";
            }

            if ('buyer_order_expenses_doc' == $documentType) {
                $queryList .= "
                    -- buyer_order_expenses_doc
                    null::integer as financial_resource_id,
                    null::integer as item_of_expenses_id,
                    null::integer as equipment_id,
                    {$alias}.financial_counteragent_id,
                    {$alias}.to_item_of_expenses_id,
                    null::integer as to_equipment_id,
                    {$alias}.maturity_date_execute,
                    0 as amount_bonus,
                    0 as amount_mutual,
                    {$alias}.to_financial_resource_id
                    ";
            }

            $queryList .= "
                from \"{$documentType}\" {$alias} " . DocumentQueryHelper::buildJoin("{$alias}") . "
                where 1 = 1
                    -- общие фильтры
            ";

            $queryWhere = "";

            if (!empty($setParameters['fromDate']))
                $queryWhere .= "
                    and {$alias}.expected_date_execute >= :fromDate";

            if (!empty($setParameters['toDate']))
                $queryWhere .= "
                    and {$alias}.expected_date_execute >= :fromDate";

            if (!empty($setParameters['inOrgDepartmentIds']))
                $queryWhere .= "
                    and {$alias}.org_department_id in (:inOrgDepartmentIds)";

            if (!empty($setParameters['inStatuses']))
                $queryWhere .= "
                    and {$alias}.status_code in (:inStatuses)";

            if (!empty($setParameters['$inCreatedBy']))
                $queryWhere .= "
                    and {$alias}.created_by in (:inCreatedBy)";


            if ('expense_simple_doc' == $documentType) {

                $queryWhere .= "
                    -- where to expense_simple_doc";

                if (!empty($setParameters['inFinancialResourcesIds']))
                    $queryWhere .= "
                        and {$alias}.financial_resource_id in (:inFinancialResourcesIds)";

                if (!empty($setParameters['inFinancialCounteragentsIds']))
                    $queryWhere .= "
                        and 1 = 0";

                if (!empty($setParameters['inItemsOfExpensesIds']))
                    $queryWhere .= "
                        and {$alias}.item_of_expenses_id in (:inItemsOfExpensesIds)";

                if (!empty($setParameters['inEquipmentsIds']))
                    $queryWhere .= "
                        and {$alias}.equipment_id in (:inEquipmentsIds)";
            }

            if ('accountable_expenses_doc' == $documentType) {
                $queryWhere .= "
                    -- where to accountable_expenses_doc";

                if (!empty($setParameters['inFinancialResourcesIds']))
                    $queryWhere .= "
                        and {$alias}.financial_resource_id in (:inFinancialResourcesIds)";

                if (!empty($setParameters['inFinancialCounteragentsIds']))
                    $queryWhere .= "
                        and {$alias}.financial_counteragent_id in (:inFinancialCounteragentsIds)";

                if (!empty($setParameters['inItemsOfExpensesIds']))
                    $queryWhere .= "
                        and {$alias}.to_item_of_expenses_id in (:inItemsOfExpensesIds)";

                if (!empty($setParameters['inEquipmentsIds']))
                    $queryWhere .= "
                        and {$alias}.to_equipment_id in (:inEquipmentsIds)";
            }

            if ('supplier_order_expenses_doc' == $documentType) {
                $queryWhere .= "
                    -- where to supplier_order_expenses_doc";

                if (!empty($setParameters['inFinancialResourcesIds']))
                    $queryWhere .= "
                        and {$alias}.financial_resource_id in (:inFinancialResourcesIds)";

                if (!empty($setParameters['inFinancialCounteragentsIds']))
                    $queryWhere .= "
                        and {$alias}.financial_counteragent_id in (:inFinancialCounteragentsIds)";

                if (!empty($setParameters['inItemsOfExpensesIds']))
                    $queryWhere .= "
                        and {$alias}.item_of_expenses_id in (:inItemsOfExpensesIds)";

                if (!empty($setParameters['inEquipmentsIds']))
                    $queryWhere .= "
                        and 1 = 0";
            }

            if ('buyer_order_expenses_doc' == $documentType) {

                $queryWhere .= "
                    -- where to buyer_order_expenses_doc";

                if (!empty($setParameters['inFinancialResourcesIds']))
                    $queryWhere .= "
                        and {$alias}.to_financial_resource_id in (:inFinancialResourcesIds)";

                if (!empty($setParameters['inFinancialCounteragentsIds']))
                    $queryWhere .= "
                        and {$alias}.financial_counteragent_id in (:inFinancialCounteragentsIds)";

                if (!empty($setParameters['inItemsOfExpensesIds']))
                    $queryWhere .= "
                        and {$alias}.to_item_of_expenses_id in (:inItemsOfExpensesIds)";

                if (!empty($setParameters['inEquipmentsIds']))
                    $queryWhere .= "
                        and 1 = 0";
            }

            $queryCount .= $queryWhere;
            $queryList .= $queryWhere;
            unset($queryWhere);

            $queryList .= "
                order by {$alias}.expected_date_execute desc, {$alias}.created_at desc, {$alias}.did" . $queryLimit;

            if (count($inDocumentType) == ++$cnt) {
                $queryCount .= "
                )";
                $queryList .= "
                )";
            } else {
                $queryCount .= "
                ),";
                $queryList .= "
                ),";
            }
        }

        $queryCount .= "
            select count(1) as total from (";

        $queryList .= ",
            cte_final as (
                select

                    id,
                    number,
                    parent_doc,
                    created_at,
                    created_by,
                    created_name,
                    completed_at,
                    completed_by,
                    completed_name,
                    registered_at,
                    registered_by,
                    registered_name,
                    title,
                    status_code,

                    document_type,
                    org_department_id,
                    amount,
                    expected_date_execute,
                    description,
                    accepted_at,
                    accepted_by,
                    rejected_at,
                    rejected_by,

                    financial_resource_id,
                    item_of_expenses_id,
                    equipment_id,
                    financial_counteragent_id,
                    to_item_of_expenses_id,
                    to_equipment_id,
                    maturity_date_execute,
                    amount_bonus,
                    amount_mutual,
                    to_financial_resource_id

                from (";

        $cnt = 0;
        foreach ($inDocumentType as $documentType) {
            $queryList .= "
                    select

                        id::integer,
                        number::integer,
                        parent_doc::json,
                        created_at::timestamp,
                        created_by::integer,
                        created_name::text,
                        completed_at::timestamp,
                        completed_by::integer,
                        completed_name::text,
                        registered_at::timestamp,
                        registered_by::integer,
                        registered_name::text,
                        title::text,
                        status_code::text,

                        document_type::text,
                        org_department_id::integer,
                        amount::integer,
                        expected_date_execute::timestamp,
                        description::text,
                        accepted_at::timestamp,
                        accepted_by::integer,
                        rejected_at::timestamp,
                        rejected_by::integer,

                        financial_resource_id::integer,
                        item_of_expenses_id::integer,
                        equipment_id::integer,
                        financial_counteragent_id::integer,
                        to_item_of_expenses_id::integer,
                        to_equipment_id::integer,
                        maturity_date_execute::timestamp,
                        amount_bonus::integer,
                        amount_mutual::integer,
                        to_financial_resource_id::integer

                    from cte_{$documentType}";

            $queryCount .= "
                    select 1
                    from cte_{$documentType}";

            if (count($inDocumentType) != ++$cnt) {

                $queryList .= "
                    union all";

                $queryCount .= "
                union all";
            }
        }

        $queryList .= "
                ) as ff
                order by ff.expected_date_execute desc, ff.created_at desc, ff.id" . $queryLimit . "
            )

        select

            cf.id,
            cf.number,
            cf.parent_doc,
            cf.created_at,
            cf.created_by,
            cf.created_name,
            cf.completed_at,
            cf.completed_by,
            cf.completed_name,
            cf.registered_at,
            cf.registered_by,
            cf.registered_name,
            cf.title,
            cf.status_code,

            cf.document_type,
            cf.org_department_id,
            cf_gp.name as org_department_name,
            cf.amount + cf.amount_bonus + cf.amount_mutual as amount,
            cf.expected_date_execute,
            cf.description,
            cf.accepted_at,
            cf.accepted_by," .
                DocumentQueryHelper::personFullName("cf_aper") . " as accepted_name,
            cf.rejected_at,
            cf.rejected_by," .
                DocumentQueryHelper::personFullName("cf_rper") . " as rejected_name,
            case
                when cf.financial_resource_id is null then cf.to_financial_resource_id
                else cf.financial_resource_id
                end as financial_resource_id,
            case
                when cf.financial_resource_id is null then cf_tfr.title
                else cf_fr.title
                end as financial_resource_name,
            case
                when cf.item_of_expenses_id is null then cf.to_item_of_expenses_id
                else cf.item_of_expenses_id
                end as item_of_expenses_id,
            case
                when cf.item_of_expenses_id is null then cf_tioe.name
                else cf_ioe.name
                end as item_of_expenses_name,
            case
                when cf.equipment_id is null then cf.to_equipment_id
                else cf.equipment_id
                end as equipment_id,
            case
                when cf.equipment_id is null then cf_teq.name
                else cf_eq.name
                end as equipment_name,
            cf.financial_counteragent_id,
            cf_fc.name as financial_counteragent_name,
            cf.maturity_date_execute

        from cte_final cf

        left join org_department         cf_gp   on cf_gp.id = cf.org_department_id
        left join financial_resource     cf_fr   on cf_fr.id = cf.financial_resource_id
        left join financial_resource     cf_tfr  on cf_tfr.id = cf.to_financial_resource_id
        left join item_of_expenses       cf_ioe  on cf_ioe.id = cf.item_of_expenses_id
        left join item_of_expenses       cf_tioe on cf_tioe.id = cf.to_item_of_expenses_id
        left join equipment              cf_eq   on cf_eq.id = cf.equipment_id
        left join equipment              cf_teq  on cf_teq.id = cf.to_equipment_id
        left join financial_counteragent cf_fc   on cf_fc.id = cf.financial_counteragent_id

        left join \"user\"           cf_ausr on cf_ausr.id = cf.accepted_by
        left join person             cf_aper on cf_aper.id = cf_ausr.person_id
        left join \"user\"           cf_rusr on cf_rusr.id = cf.rejected_by
        left join person             cf_rper on cf_rper.id = cf_rusr.person_id
        ";

        $queryCount .= "
            ) as ff";

        $em = $this->getDoctrine()->getManager();

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('total', 'total', 'integer');
        $total = $em->createNativeQuery($queryCount, $rsm)
                ->setParameters($setParameters)
                ->getSingleScalarResult();

        $documents = $em->createNativeQuery($queryList, new DTORSM(DTO\ListExpensesDocumentDTO::class, DTORSM::ARRAY_INDEX))
                ->setParameters($setParameters)
                ->getResult('DTOHydrator');

        return new DTO\ListExpensesDTO($documents, $total);
    }

}
