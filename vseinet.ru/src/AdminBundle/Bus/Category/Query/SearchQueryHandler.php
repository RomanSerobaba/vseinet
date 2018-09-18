<?php 

namespace AdminBundle\Bus\Category\Query;

use AppBundle\Bus\Message\MessageHandler;

class SearchQueryHandler extends MessageHandler
{
    public function handle(SearchQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT 
                NEW AdminBundle\Bus\Category\Query\DTO\CategoryFound (
                    c.id,
                    c.name,
                    c.pid
                ),
                CASE WHEN c.name LIKE :qo THEN 1 ELSE 2 END AS HIDDEN ORD
            FROM AppBundle:Category AS c 
            WHERE c.aliasForId IS NULL 
                AND (CASE WHEN EXISTS (
                    SELECT 1 
                    FROM AppBundle:Category AS cc 
                    WHERE cc.pid = c.id
                ) THEN false ELSE true END) = true 
                AND (c.name LIKE :q1 OR c.name LIKE :q2) 
            ORDER BY ORD, c.name  
        ");
        $q->setParameter('q1', $query->q.'%');
        $q->setParameter('q2', '%'.$query->q.'%');
        $q->setParameter('qo', $query->q.'%');
        $q->setMaxResults($query->limit);
        $result = $q->getResult('IndexByHydrator');
        if (!empty($result)) {
            $q = $em->createQuery("
                SELECT 
                    NEW AdminBundle\Bus\Category\Query\DTO\CategoryFound (
                        c.id,
                        c.name,
                        cp.id 
                    )
                FROM AppBundle:Category AS c 
                INNER JOIN AppBundle:CategoryPath AS cp WITH cp.pid = c.id 
                WHERE c.aliasForId IS NULL AND c.id > 0 AND cp.id IN (:ids)
                ORDER BY cp.level 
            ");
            $q->setParameter('ids', array_keys($result));
            foreach ($q->getResult() as $item) {
                $items[$item->pid][$item->id] = $item->name;
            }
            foreach ($items as $id => $path) {
                $result[$id]->name = implode(' / ', $path);
                array_pop($path);
                $result[$id]->path = $path;
            }
        }

        return array_values($result);
    }
}
