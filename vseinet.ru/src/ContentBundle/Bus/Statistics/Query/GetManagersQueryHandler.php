<?php 

namespace ContentBundle\Bus\Statistics\Query;

use AppBundle\Bus\Message\MessageHandler;
use ContentBundle\Bus\Statistics\Query\DTO\ManagerGroup;

class GetManagersQueryHandler extends MessageHandler
{
    public function handle(GetManagersQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $groups = [];

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Statistics\Query\DTO\Manager (
                    m.userId, 
                    TRIM(CONCAT(p.lastname, ' ', p.firstname, ' ', p.secondname)) 
                )
            FROM ContentBundle:Manager m 
            INNER JOIN AppBundle:User u WITH u.id = m.userId 
            INNER JOIN AppBundle:Person p WITH p.id = u.personId
            WHERE EXISTS (
                SELECT 1 
                FROM AppBundle:Role r  
                INNER JOIN AppBundle:Subrole s WITH s.roleId = r.id
                INNER JOIN AppBundle:UserToSubrole u2s WITH u2s.subroleId = s.id AND u2s.userId = u.id  
                WHERE r.code = 'CONTENTER'  
            ) AND m.isActive = true
            ORDER BY p.lastname, p.firstname, p.secondname
        ");
        $managers = $q->getArrayResult();
        if (!empty($managers)) {
            $groups[] = new ManagerGroup('Контент-менеджеры', $managers);
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Statistics\Query\DTO\Manager (
                    m.userId,
                    TRIM(CONCAT(p.lastname, ' ', p.firstname, ' ', p.secondname)) 
                )
            FROM ContentBundle:Manager m 
            INNER JOIN AppBundle:User u WITH u.id = m.userId 
            INNER JOIN AppBundle:Person p WITH p.id = u.personId
            WHERE NOT EXISTS (
                SELECT 1 
                FROM AppBundle:Role r  
                INNER JOIN AppBundle:Subrole s WITH s.roleId = r.id 
                INNER JOIN AppBundle:UserToSubrole u2s WITH u2s.subroleId = s.id AND u2s.userId = u.id  
                WHERE r.code = 'CONTENTER' 
            ) AND m.isActive = true
            GROUP BY m.userId, p.id
            ORDER BY p.lastname, p.firstname, p.secondname
        ");
        $managers = $q->getArrayResult();
        if (!empty($managers)) {
            $groups[] = new ManagerGroup('Магазин / офис', $managers);
        }

        return $groups;
    }
}