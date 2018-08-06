<?php

namespace FinanseBundle\Bus\AccountableExpensesDoc\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\ORM\Query\DTORSM;
use DocumentBundle\SimpleTools\DocumentQueryHelper;

class ItemQueryHandler extends MessageHandler
{

    public function handle(ItemQuery $query)
    {

        $queryText = "
            select" . DocumentQueryHelper::buildSelect('aed') . ",
                -- персональные поля документа

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
                aed.accepted_by," .
                DocumentQueryHelper::personFullName("aed_aper") . " as accepted_name,
                aed.rejected_at,
                aed.rejected_by," .
                DocumentQueryHelper::personFullName("aed_rper") . " as rejected_name,
                aed.payment_at,
                aed.payment_by," .
                DocumentQueryHelper::personFullName("aed_pper") . " as payment_name


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
            left join \"user\"                 aed_pusr on aed_pusr.id = aed.payment_by
            left join person                   aed_pper on aed_pper.id = aed_pusr.person_id

            where
                aed.did = {$query->id}
            ";

        $result = $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, new DTORSM(DTO\DocumentHead::class, DTORSM::ARRAY_INDEX))
                ->getOneOrNullResult('DTOHydrator');

        if (empty($result))
            throw new NotFoundHttpException('Документ не найден');

        return $result;
    }

}
