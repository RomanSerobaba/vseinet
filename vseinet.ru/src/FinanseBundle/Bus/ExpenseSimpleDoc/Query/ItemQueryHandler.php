<?php

namespace FinanseBundle\Bus\ExpenseSimpleDoc\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\ORM\Query\DTORSM;
use DocumentBundle\SimpleTools\DocumentQueryHelper;

class ItemQueryHandler extends MessageHandler
{
    public function handle(ItemQuery $query)
    {

        $queryText = "
            select" . DocumentQueryHelper::buildSelect('esd') . ",
                -- персональные поля документа

                esd.org_department_id,
                esd_gp.name as org_department_name,
                esd.equipment_id,
                case when esd_eq.is_car then concat(esd_eq.name, '(', esd_eq.reg_number, ')')
                    else esd_eq.name
                    end as equipment_name,
                esd.amount,
                esd.item_of_expenses_id,
                esd_ioe.name as item_of_expenses_name,
                esd.expected_date_execute,
                esd.description,
                esd.financial_resource_id,
                esd_fr.title as financial_resource_name,
                esd.accepted_at,
                esd.rejected_at,
                esd.accepted_by," .
                DocumentQueryHelper::personFullName("esd_aper") . " as accepted_name,
                esd.rejected_by," .
                DocumentQueryHelper::personFullName("esd_rper") . " as rejected_name

            from expense_simple_doc esd" . DocumentQueryHelper::buildJoin('esd') . "
            -- персональные данные документа

            left join item_of_expenses   esd_ioe on esd_ioe.id = esd.item_of_expenses_id
            left join org_department     esd_gp  on esd_gp.id = esd.org_department_id
            left join financial_resource esd_fr  on esd_fr.id = esd.financial_resource_id
            left join equipment          esd_eq  on esd_eq.id = esd.equipment_id
            left join \"user\"           esd_ausr on esd_ausr.id = esd.accepted_by
            left join person             esd_aper on esd_aper.id = esd_ausr.person_id
            left join \"user\"           esd_rusr on esd_rusr.id = esd.rejected_by
            left join person             esd_rper on esd_rper.id = esd_rusr.person_id

            where
                esd.did = {$query->id}
            ";

        $result = $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, new DTORSM(DTO\DocumentHead::class, DTORSM::ARRAY_INDEX))
                ->getOneOrNullResult('DTOHydrator');

        if (empty($result)) throw new NotFoundHttpException('Документ не найден');

        return $result;

    }

}
