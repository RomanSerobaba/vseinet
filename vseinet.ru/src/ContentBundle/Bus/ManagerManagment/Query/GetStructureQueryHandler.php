<?php 

namespace ContentBundle\Bus\ManagerManagment\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetStructureQueryHandler extends MessageHandler
{
    public function handle(GetStructureQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $roots = [
            -1 => new DTO\Department(-1, 'Группы'),
            -2 => new DTO\Department(-2, 'Контент-менеджеры'),
            -3 => new DTO\Department(-3, 'Магазин/офис'),
        ];

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\ManagerManagment\Query\DTO\Group (
                    mg.id,
                    mg.name
                )
            FROM ContentBundle:ManagerGroup mg 
            ORDER BY mg.name 
        ");
        $groups = $q->getResult('IndexByHydrator');
        foreach ($groups as $group) {
            $roots[-1]->groupIds[] = $group->id;
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\ManagerManagment\Query\DTO\Department (
                    d.id,
                    d.name
                )
            FROM OrgBundle:Department d 
            INNER JOIN OrgBundle:Employee e WITH e.departmentId = d.id 
            INNER JOIN ContentBundle:Manager m WITH m.userId = e.userId 
            INNER JOIN AppBundle:UserToSubrole u2sr WITH u2sr.userId = e.userId
            INNER JOIN AppBundle:Subrole sr WITH sr.id = u2sr.subroleId
            INNER JOIN AppBundle:Role r WITH r.id = sr.roleId 
            WHERE r.code = 'CONTENTER'
            GROUP BY d.id 
            ORDER BY d.sortOrder
        ");
        $departments = $q->getResult('IndexByHydrator');
        foreach ($departments as $department) {
            $roots[-2]->departmentIds[] = $department->id;
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\ManagerManagment\Query\DTO\Manager (
                    m.userId,
                    m.groupId,
                    CASE WHEN EXISTS (
                        SELECT 1 
                        FROM AppBundle:Role r 
                        INNER JOIN AppBundle:Subrole sr WITH sr.roleId = r.id 
                        INNER JOIN AppBundle:UserToSubrole u2sr WITH u2sr.subroleId = sr.id 
                        WHERE u2sr.userId = u.id AND r.code = 'CONTENTER'
                    ) 
                    THEN e.departmentId ELSE 0 END,
                    TRIM(CONCAT(COALESCE(p.lastname, ''), ' ', COALESCE(p.firstname, ''), ' ', COALESCE(p.secondname, ''))),
                    GROUP_CONCAT(DISTINCT t.categoryId SEPARATOR ',')
                )
            FROM ContentBundle:Manager m 
            INNER JOIN AppBundle:User u WITH u.id = m.userId 
            INNER JOIN AppBundle:Person p WITH p.id = u.personId
            INNER JOIN OrgBundle:Employee e WITH e.userId = u.id 
            LEFT OUTER JOIN ContentBundle:Task t WITH t.managerId = u.id 
            WHERE m.isActive = true
            GROUP BY m.userId, e.userId, u.id, p.id
            ORDER BY p.lastname, p.firstname, p.secondname
        ");
        $managers = $q->getArrayResult(); 
        foreach ($managers as $manager) {
            if ($manager->groupId) {
                $groups[$manager->groupId]->managerIds[] = $manager->userId;
            }
            if ($manager->departmentId) {
                $departments[$manager->departmentId]->managerIds[] = $manager->userId;
            }
            if (!$manager->groupId && !$manager->departmentId) {
                $manager->departmentId = -3;
                $roots[-3]->managerIds[] = $manager->userId;    
            }
        }

        return new DTO\Structure($roots, $departments, $groups, $managers);
    } 
}
