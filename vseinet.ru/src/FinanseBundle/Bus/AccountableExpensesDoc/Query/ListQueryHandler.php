<?php

namespace FinanseBundle\Bus\AccountableExpensesDoc\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use AppBundle\ORM\Query\DTORSM;
use DocumentBundle\SimpleTools\DocumentQueryHelper;
use FinanseBundle\Bus\AccountableExpensesDoc\Query\DTO\DocumentList;

class ListQueryHandler extends MessageHandler
{

    public function handle(ListQuery $query)
    {


        $setParameters = [];
        $em = $this->getDoctrine()->getManager();

        // Запрос-счетчик

        $queryCount = "
            select
                count(did) as total
            from accountable_expenses_doc aed
            ";

        // Запрос-список

        $queryList = "
            select" . DocumentQueryHelper::buildSelect('aed') . ",

                aed.org_department_id,
                aed_gp.name as org_department_name,
                aed.financial_counteragent_id,
                aed_fc.name as financial_counteragent_name,
                aed.amount,
                aed.to_item_of_expenses_id,
                aed_ioe.name as to_item_of_expenses_name,
                aed.to_equipment_id,
                aed_eq.name as to_equipment_name,
                aed.expected_date_execute,
                aed.maturity_date_execute,
                aed.description,
                aed.financial_resource_id,
                aed_fr.title as financial_resource_name,
                aed.accepted_at,
                aed.rejected_at,
                aed.accepted_by," .
                DocumentQueryHelper::personFullName("aed_aper") . " as accepted_name,
                aed.rejected_by," .
                DocumentQueryHelper::personFullName("aed_rper") . " as rejected_name

            from accountable_expenses_doc aed" . DocumentQueryHelper::buildJoin('aed') . "

            left join item_of_expenses         aed_ioe  on aed_ioe.id = aed.to_item_of_expenses_id
            left join org_department           aed_gp   on aed_gp.id = aed.org_department_id
            left join financial_resource       aed_fr   on aed_fr.id = aed.financial_resource_id
            left join financial_counteragent   aed_fc   on aed_fc.id = aed.financial_counteragent_id
            left join equipment                aed_eq   on aed_eq.id = aed.to_equipment_id
            left join \"user\"                 aed_ausr on aed_ausr.id = aed.accepted_by
            left join person                   aed_aper on aed_aper.id = aed_ausr.person_id
            left join \"user\"                 aed_rusr on aed_rusr.id = aed.rejected_by
            left join person                   aed_rper on aed_rper.id = aed_rusr.person_id
            ";

        // Просмотр архива

        if (!$query->withCompleted) {

            $queryList .= "
                where aed.completed_at is null";

            $queryCount .= "
                where aed.completed_at is null";
        } else {

            $queryList .= "
                where 1 = 1";

            $queryCount .= "
                where 1 = 1";
        }

        // Фильтр по статусу

        if (!empty($query->inStatuses)) {

            $queryList .= "
                and aed.status_code in (:inStatuses)";

            $queryCount .= "
                and aed.status_code in (:inStatuses)";

            $setParameters['inStatuses'] = $query->inStatuses;
        }

        // Фильтр по автору

        if (!empty($query->inCreatedBy)) {

            $queryList .= "
                and aed.created_by in (:inCreatedBy)";

            $queryCount .= "
                and aed.created_by in (:inCreatedBy)";

            $setParameters['inCreatedBy'] = $query->inCreatedBy;
        }

        //////////// Общее управление списком
        // Интервал дат

        if ($query->fromDate) {
            $queryList .= "
                and aed.created_at >= :fromDate";
            $queryCount .= "
                and aed.created_at >= :fromDate";
            $setParameters['fromDate'] = $query->fromDate . 'T00:00:00';
        }
        if ($query->toDate) {

            $queryList .= "
                and aed.created_at <= :toDate";
            $queryCount .= "
                and aed.created_at <= :toDate";
            $setParameters['toDate'] = $query->toDate . 'T23:59:59';
        }

        /////////////////////////////////////////////////////////////
        //
        //  Тут идет подсчет общего числа элементов списка,
        //  дальше обрабатывается только запрос списка
        //

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('total', 'total', 'integer');
        $totoal = $em->createNativeQuery($queryCount, $rsm)
                ->setParameters($setParameters)
                ->getSingleScalarResult();

        /////////////////////////////////////////////////////////////

        $queryList .= "
            order by aed.created_at desc, aed.did desc";

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
