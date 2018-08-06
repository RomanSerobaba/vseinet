<?php 

namespace OrgBundle\Bus\Work\Query;

use OrgBundle\Bus\Department\Query\DTO\Department;
use AppBundle\Bus\Message\MessageHandler;
use OrgBundle\Entity\Employee;

class GetOrgStructureForSalaryQueryHandler extends MessageHandler
{
    /**
     * @param GetOrgStructureForSalaryQuery $query
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    public function handle(GetOrgStructureForSalaryQuery $query)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $since = date('Y-m-01', $query->date ? strtotime($query->date) : time());
        $till = date('Y-m-d', strtotime('last day of this month ' . $since));


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
            ->setParameter('since', new \DateTime($since))
            ->setParameter('till', new \DateTime($till))
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


        /** @var DTO\Employee[] $employees */
        $employees = $em->createQuery('
                SELECT
                    NEW OrgBundle\Bus\Work\Query\DTO\Employee (
                        ed.employeeUserId,
                        ed.departmentId,
                        TRIM(CONCAT(
                            COALESCE(CONCAT(pp.lastname, \' \'), \'\'),
                            COALESCE(CONCAT(pp.firstname, \' \'), \'\'),
                            COALESCE(pp.secondname, \'\')
                        )),
                        em.sortOrder,
                        em.position,
                        ss.amount,
                        ss.tax,
                        ss.paid,
                        ss.fines,
                        ss.coefficient,
                        ss.queueAmount
                    )
                FROM OrgBundle:EmployeeToDepartment AS ed
                    INNER JOIN OrgBundle:EmploymentHistory AS eh
                        WITH ed.employeeUserId = eh.employeeUserId
                            AND eh.hiredAt <= :till
                            AND (eh.firedAt IS NULL OR eh.firedAt >= :since)
                    INNER JOIN OrgBundle:Employee AS em
                        WITH ed.employeeUserId=em.userId
                    LEFT JOIN AppBundle:User AS uu
                        WITH ed.employeeUserId = uu.id
                    LEFT JOIN AppBundle:Person AS pp
                        WITH uu.personId = pp.id
                    LEFT JOIN OrgBundle:Salary AS ss
                        WITH ed.employeeUserId = ss.employeeId
                            AND ss.date >= :since AND ss.date <= :till
                WHERE (ed.isSynthetic IS NULL OR ed.isSynthetic = FALSE)
                    AND ed.activeSince <= :till
                    AND (ed.activeTill IS NULL OR ed.activeTill >= :since)
                ORDER BY ed.departmentId, em.position, em.sortOrder
            ')
            ->setParameter('since', new \DateTime($since))
            ->setParameter('till', new \DateTime($till))
            ->getResult();

        foreach ($employees AS &$employee) {
            if (isset($departments[$employee->departmentId])) {
                /** @var Department $department */
                $department = $departments[$employee->departmentId];

                if ($employee->position == Employee::POSITION_CHIEF) {
                    $department->chiefId = $employee->employeeId;
                } elseif ($employee->position == Employee::POSITION_DEPUTY) {
                    $department->deputyId = $employee->employeeId;
                } else {
                    $department->employeesIds[] = $employee->employeeId;
                }
            }
        }

        return ['departments' => array_values($departments), 'employees' => $employees];
    }
}
