<?php

namespace OrgBundle\Bus\Department\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;

class FoundResultsQueryHandler extends MessageHandler
{
    public function handle(FoundResultsQuery $query)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $sql = '
            SELECT
              d.id,
              d.name,
              d.number,
              d.department_type_code AS type_code,
              string_agg(d2.name, \' > \' ORDER BY dp.plevel) AS path
            FROM org_department AS d
              INNER JOIN org_department_to_department AS dd
                ON d.id = dd.org_department_id AND d.pid = dd.pid
                  AND dd.active_since <= CURRENT_TIMESTAMP
                  AND (dd.active_till IS NULL OR dd.active_till >= CURRENT_TIMESTAMP)
              LEFT JOIN org_department_path AS dp
                ON dp.org_department_id = d.id
              LEFT JOIN org_department AS d2
                ON dp.pid = d2.id
                  AND d2.number IS NOT NULL
            GROUP BY d.id, d.name, d.number, d.department_type_code';
        $params = [];

        if ($query->q) {
            $sql .= ' HAVING string_agg(d2.name, \' > \') LIKE :query';
            $params['query'] = '%' . $query->q . '%';
        }

        $sql .= ' ORDER BY d.number';

        if ($query->limit) {
            $sql .= ' LIMIT :limit';
            $params['limit'] = $query->limit;
        }


        $result = $em->createNativeQuery($sql, new DTORSM(DTO\DepartmentResult::class))
            ->setParameters($params)
            ->getResult('DTOHydrator');

        return $result;
    }
}