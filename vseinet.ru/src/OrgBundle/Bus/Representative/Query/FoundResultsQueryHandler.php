<?php

namespace OrgBundle\Bus\Representative\Query;

use AppBundle\Bus\Message\MessageHandler;

class FoundResultsQueryHandler extends MessageHandler
{
    public function handle(FoundResultsQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $sql = '
            SELECT
                NEW OrgBundle\Bus\Representative\Query\DTO\Point (
                    gp.id,
                    gp.name
                )
            FROM OrgBundle:Representative AS r
                INNER JOIN OrgBundle:GeoPoint AS gp
                    WITH r.geoPointId = gp.id
            WHERE 1=1';
        $params = [];
        if ($query->q) {
            $sql .= ' AND gp.name LIKE :query';
            $params['query'] = '%' . $query->q . '%';
        }
        if (!$query->withInactives) {
            $sql .= ' AND r.isActive = TRUE';
        }
        $sql .= '
            ORDER BY gp.name, gp.id';

        $result = $em->createQuery($sql)
            ->setParameters($params);

        if ($query->limit) {
            $result->setMaxResults($query->limit);
        }

        return $result->getResult();
    }
}