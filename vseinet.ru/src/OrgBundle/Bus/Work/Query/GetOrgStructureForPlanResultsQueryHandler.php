<?php

namespace OrgBundle\Bus\Work\Query;

use AppBundle\Bus\Message\MessageHandler;
use OrgBundle\Bus\Department\Query\DTO\Department;
use OrgBundle\Bus\Employee\Query\DTO\EmployeeInfo;
use OrgBundle\Entity\Employee;

class GetOrgStructureForPlanResultsQueryHandler extends MessageHandler
{
    /**
     * @param GetOrgStructureForPlanResultsQuery $query
     * @return array
     */
    public function handle(GetOrgStructureForPlanResultsQuery $query)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $query->since = new \DateTime(date('Y-m-01', $query->since ? strtotime($query->since) : time()) . ' 00:00:00');
        $query->till  = new \DateTime(date('Y-m-d', strtotime('last day of this month ' .
                                            ($query->till ? $query->till : $query->since->format('Y-m-d')))) . ' 23:59:59');

        /** @var Department[] $departmentsRes */
        $departmentsRes = $em->createQuery('
                SELECT
                    NEW OrgBundle\Bus\Department\Query\DTO\Department (
                        d.pid,
                        d.id,
                        d.name,
                        d.typeCode,
                        CASE WHEN dd.activeSince IS NOT NULL THEN TRUE ELSE FALSE END,
                        d.number,
                        d.sortOrder
                    )
                FROM
                    OrgBundle:Department AS d
                    INNER JOIN OrgBundle:DepartmentToDepartment AS dd
                        WITH d.id=dd.departmentId
                            AND (d.pid=dd.pid OR (d.pid IS NULL AND dd.pid IS NULL))
                    LEFT JOIN OrgBundle:DepartmentPath AS dp
                        WITH d.id=dp.departmentId AND d.id=dp.pid
                WHERE dd.activeSince <= :till
                    AND (dd.activeTill IS NULL OR dd.activeTill >= :since)
                ORDER BY dp.level, d.sortOrder
            ')
            ->setParameter('since', $query->since)
            ->setParameter('till',  $query->till)
            ->getResult();

        /** @var Department[] $departments */
        $departments = [];

        foreach ($departmentsRes as &$department) {
            $departments[$department->id] = $department;
            if ($department->pid < 0) {
                $department->pid = null;
            }
            if (isset($departments[$department->pid])) {
                $departments[$department->pid]->childrenIds[] = $department->id;
            }
        }


        /** @var EmployeeInfo[] $employeesRes */
        $employeesRes = $em->createQuery('
                SELECT
                    NEW OrgBundle\Bus\Employee\Query\DTO\EmployeeInfo (
                        em.userId,
                        ed.departmentId,
                        dd.number,
                        dd.name,
                        TRIM(CONCAT(
                            COALESCE(CONCAT(pp.lastname, \' \'), \'\'),
                            COALESCE(CONCAT(pp.firstname, \' \'), \'\'),
                            COALESCE(pp.secondname, \'\')
                        )),
                        em.sortOrder,
                        em.position,
                        eh.hiredAt,
                        eh.firedAt,
                        CASE
                            WHEN ed.activeSince IS NOT NULL
                            THEN TRUE
                            ELSE FALSE
                        END
                    )
                FROM
                    OrgBundle:Employee AS em
                    INNER JOIN OrgBundle:EmploymentHistory AS eh
                        WITH em.userId = eh.employeeUserId
                            AND (eh.hiredAt <= :till)
                            AND (eh.firedAt IS NULL OR eh.firedAt >= :since)
                    LEFT JOIN OrgBundle:EmployeeToDepartment AS ed
                        WITH ed.employeeUserId = em.userId AND ed.isSynthetic = false
                            AND (ed.activeSince <= :till)
                            AND (ed.activeTill IS NULL OR ed.activeTill >= :since)
                    LEFT JOIN OrgBundle:Department AS dd
                        WITH ed.departmentId = dd.id
                    LEFT JOIN AppBundle:User AS uu
                        WITH em.userId = uu.id
                    LEFT JOIN AppBundle:Person AS pp
                        WITH uu.personId = pp.id
                ORDER BY ed.activeSince
            ')
            ->setParameter('since', $query->since)
            ->setParameter('till',  $query->till)
            ->getResult();

        foreach ($employeesRes AS &$employee) {
            if (isset($departments[$employee->departmentId])) {
                /** @var Department $department */
                $department = $departments[$employee->departmentId];

                if ($employee->position == Employee::POSITION_CHIEF) {
                    $department->chiefId = $employee->userId;
                } elseif ($employee->position == Employee::POSITION_DEPUTY) {
                    $department->deputyId = $employee->userId;
                } else {
                    $department->employeesIds[] = $employee->userId;
                }
            }
        }

        return [
            'departments' => array_values($departments),
            'employees' => $employeesRes
        ];
    }
}