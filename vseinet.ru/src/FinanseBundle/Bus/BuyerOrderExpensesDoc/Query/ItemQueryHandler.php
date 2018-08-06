<?php

namespace FinanseBundle\Bus\BuyerOrderExpensesDoc\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\ORM\Query\DTORSM;
use DocumentBundle\SimpleTools\DocumentQueryHelper;

class ItemQueryHandler extends MessageHandler
{

    public function handle(ItemQuery $query)
    {

        $queryText = "
            select" . DocumentQueryHelper::buildSelect('boed') . ",
                -- персональные поля документа

                boed.org_department_id,
                boed_gp.name as org_department_name,
                boed.financial_counteragent_id,
                boed_fc.name as financial_counteragent_name,
                boed.amount,
                boed.to_item_of_expenses_id,
                boed_ioe.name as to_item_of_expenses_name,
                boed.expected_date_execute,
                boed.maturity_date_execute,
                boed.description,
                boed.to_financial_resource_id,
                boed_fr.title as to_financial_resource_name,
                boed.accepted_at,
                boed.accepted_by," .
                DocumentQueryHelper::personFullName("boed_aper") . " as accepted_name,
                boed.rejected_at,
                boed.rejected_by," .
                DocumentQueryHelper::personFullName("boed_rper") . " as rejected_name

            from buyer_order_expenses_doc boed" . DocumentQueryHelper::buildJoin('boed') . "

            left join item_of_expenses         boed_ioe  on boed_ioe.id = boed.to_item_of_expenses_id
            left join org_department           boed_gp   on boed_gp.id = boed.org_department_id
            left join financial_resource       boed_fr   on boed_fr.id = boed.to_financial_resource_id
            left join financial_counteragent   boed_fc   on boed_fc.id = boed.financial_counteragent_id
            left join \"user\"                 boed_ausr on boed_ausr.id = boed.accepted_by
            left join person                   boed_aper on boed_aper.id = boed_ausr.person_id
            left join \"user\"                 boed_rusr on boed_rusr.id = boed.rejected_by
            left join person                   boed_rper on boed_rper.id = boed_rusr.person_id
            where
                boed.did = {$query->id}
            ";

        $result = $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, new DTORSM(DTO\DocumentHead::class, DTORSM::ARRAY_INDEX))
                ->getOneOrNullResult('DTOHydrator');

        if (empty($result))
            throw new NotFoundHttpException('Документ не найден');

        return $result;
    }

}
