<?php 

namespace ContentBundle\Bus\ManagerManagment\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetCategoriesQueryHandler extends MessageHandler
{
    public function handle(GetCategoriesQuery $query)
    {
        switch ($query->filter) {
            case 'all':
                $where = "";
                break;

            case 'with-tpl':
                $where = " AND c2.tpl != 'none'";
                break;

            case 'without-contenter':
                $where = " AND t.id IS NULL";
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW ContentBundle\Bus\ManagerManagment\Query\DTO\Category (
                    c.id,
                    c.name,
                    c.pid,
                    c.tpl,
                    c.isTplEnabled,
                    GROUP_CONCAT(DISTINCT cc.id SEPARATOR ','),
                    GROUP_CONCAT(DISTINCT t.managerId SEPARATOR ',')
                )
            FROM ContentBundle:Category c 
            LEFT OUTER JOIN ContentBundle:Category cc WITH cc.pid = c.id
            INNER JOIN ContentBundle:CategoryPath cp WITH cp.pid = c.id 
            INNER JOIN ContentBundle:Category c2 WITH c2.id = cp.id 
            LEFT OUTER JOIN ContentBundle:Task t WITH t.categoryId = c2.id 
            WHERE c.pid = :pid {$where}
            GROUP BY c.id
            ORDER BY c.name 
        ");
        $q->setParameter('pid', $query->pid);

        return $q->getResult();
    } 
}
