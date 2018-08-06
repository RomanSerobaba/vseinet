<?php

namespace OrgBundle\Bus\Counteragents\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;

class GetFinancialResourcesQueryHandler extends MessageHandler
{
    public function handle(GetFinancialResourcesQuery $query): array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $qParam = [];
        $qText = '
            SELECT fr.id, fr.title, pg_class.relname AS "type",
                CASE
                    WHEN frcd.id IS NOT NULL THEN
                        json_build_object(
                            \'geo_room_id\', frcd.geo_room_id,
                            \'collector_id\', frcd.collector_id,
                            \'org_department_id\', frcd.org_department_id)
                    WHEN frsa.id IS NOT NULL THEN
                        json_build_object(
                            \'number\', frsa.number,
                            \'bank_id\', frsa.bank_id,
                            \'counteragent_id\', frsa.counteragent_id)
                    WHEN frbc.id IS NOT NULL THEN
                        json_build_object(
                            \'number\', frbc.number,
                            \'bank_id\', frbc.bank_id,
                            \'owner_id\', frbc.owner_id)
                    WHEN frew.id IS NOT NULL THEN
                        json_build_object(
                            \'number\', frew.number,
                            \'payment_system\', frew.payment_system,
                            \'owner_id\', frew.owner_id)
                    ELSE
                        NULL
                END AS data
            FROM financial_resource AS fr
                LEFT JOIN pg_class
                    ON pg_class.oid = fr.TABLEOID
                LEFT JOIN cash_desk AS frcd
                    ON fr.id = frcd.id
                LEFT JOIN settlement_account AS frsa
                    ON fr.id = frsa.id
                LEFT JOIN bank_card AS frbc
                    ON fr.id = frbc.id
                LEFT JOIN e_wallet AS frew
                    ON fr.id = frew.id';

        if ($query->type) {
            $qText .= '
                WHERE pg_class.relname = :type';
            $qParam['type'] = $query->type;
        }

        $res = $em->createNativeQuery($qText, new DTORSM(DTO\FinancialResource::class))
            ->setParameters($qParam)
            ->getResult('DTOHydrator');

        return $res;
    }
}