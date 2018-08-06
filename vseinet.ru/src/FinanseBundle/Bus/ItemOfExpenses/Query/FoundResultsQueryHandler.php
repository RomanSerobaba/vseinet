<?php

namespace FinanseBundle\Bus\ItemOfExpenses\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;
use Doctrine\ORM\Query\ResultSetMapping;

class FoundResultsQueryHandler extends MessageHandler
{

    public function handle(FoundResultsQuery $query)
    {
        if (empty($query->q)) {
            $q = '%';
        } else {
            $q = '%' . mb_strtolower($query->q) . '%';
        }

        $queryList = "
            with

                all_need_types as (
                    select
                        id,
                        pid,
                        name
                    from item_of_expenses ioe
                    where
                        not ioe.is_group and
                        ioe.is_active and
                        (
                            lower(ioe.name) like :q or
                            lower(ioe.search_tags) like :q
                        )
                ),

                all_need_groups as (
                    select
                        ioeg.id,
                        ioeg.pid,
                        array_to_json(
                            array(
                                select ant1.id from all_need_types ant1 where ant1.pid = ioeg.id
                            )
                        ) as children_ids,
                        ioeg.name
                    from item_of_expenses ioeg
                    where
                        ioeg.is_group and
                        id in (select distinct pid from all_need_types)
                )

                select
                    array_to_json(
                        array(
                            select row_to_json(ang) from all_need_groups ang
                        )
                    ) as groups,
                    array_to_json(
                        array(
                            select row_to_json(ant) from all_need_types ant
                        )
                    ) as items

            ";

        $res = $this->getDoctrine()->getManager()
                        ->createNativeQuery($queryList, new DTORSM(DTO\ListDTO::class, DTORSM::ARRAY_INDEX))
                        ->setParameters(['q' => $q])
                        ->getResult('DTOHydrator')[0];

        return $res;
    }

}
