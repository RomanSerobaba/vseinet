<?php

namespace FinanseBundle\Bus\SupplierOrderExpensesDoc\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\ORM\Query\DTORSM;
use DocumentBundle\SimpleTools\DocumentQueryHelper;

class ItemQueryHandler extends MessageHandler
{
    public function handle(ItemQuery $query)
    {

        $queryText = "
            select" . DocumentQueryHelper::buildSelect('soed') . ",
                -- персональные поля документа

                soed.org_department_id,
                soed_gp.name as org_department_name,
                soed.financial_counteragent_id,
                soed_fc.name as financial_counteragent_name,
                soed.amount_bonus,
                soed.amount_mutual,
                soed.amount,
                soed.item_of_expenses_id,
                soed_ioe.name as item_of_expenses_name,
                soed.expected_date_execute,
                soed.description,
                soed.financial_resource_id,
                soed_fr.title as financial_resource_name,
                soed.accepted_at,
                soed.rejected_at,
                soed.accepted_by," .
                DocumentQueryHelper::personFullName("soed_aper") . " as accepted_by_name,
                soed.rejected_by," .
                DocumentQueryHelper::personFullName("soed_rper") . " as rejected_by_name,
                array_to_json(array(
                    select
                        json_build_object(
                            'id', ad_relative.did,
                            'title', ad_relative.title,
                            'document_type', pg_relative.relname)
                    from any_doc* ad_relative
                    left join pg_class pg_relative on pg_relative.oid = ad_relative.tableoid
                    where to_jsonb(ad_relative.did) <@ soed.relative_documents_ids
                )) as relative_documents

            from supplier_order_expenses_doc soed" . DocumentQueryHelper::buildJoin('soed') . "
            -- персональные данные документа

            left join financial_counteragent soed_fc on soed_fc.id = soed.financial_counteragent_id
            left join item_of_expenses   soed_ioe on soed_ioe.id = soed.item_of_expenses_id
            left join org_depatment      soed_gp  on soed_gp.id = soed.org_department_id
            left join financial_resource soed_fr  on soed_fr.id = soed.financial_resource_id
            left join \"user\"           soed_ausr on soed_ausr.id = soed.accepted_by
            left join person             soed_aper on soed_aper.id = soed_ausr.person_id
            left join \"user\"           soed_rusr on soed_rusr.id = soed.accepted_by
            left join person             soed_rper on soed_rper.id = soed_rusr.person_id

            where
                soed.did = {$query->id}
            ";

        $result = $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, new DTORSM(DTO\DocumentHead::class, DTORSM::ARRAY_INDEX))
                ->getOneOrNullResult('DTOHydrator');

        if (empty($result)) throw new NotFoundHttpException('Документ не найден');

        return $result;

    }

}
