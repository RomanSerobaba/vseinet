<?php 

namespace OrgBundle\Bus\Department\Query;

use OrgBundle\Bus\Department\Query\DTO;
use AppBundle\Bus\Message\MessageHandler;
use OrgBundle\Entity\Employee;

class GetStructureQueryHandler extends MessageHandler
{
    /**
     * @param GetStructureQuery $query
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    public function handle(GetStructureQuery $query)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var DTO\Department[] $departmentsRes */
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
                WHERE
                    (dd.activeSince IS NULL OR dd.activeSince <= CURRENT_TIMESTAMP())
                    AND (dd.activeTill IS NULL OR dd.activeTill >= CURRENT_TIMESTAMP())
                ORDER BY dp.level, d.sortOrder
            ')
            ->getResult();

        /** @var DTO\Department[] $departments */
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


        $q = $em->createQuery('
            SELECT
                em AS employee,
                ed.departmentId,
                TRIM(CONCAT(
                    COALESCE(CONCAT(pp.lastname, \' \'), \'\'),
                    COALESCE(CONCAT(pp.firstname, \' \'), \'\'),
                    COALESCE(pp.secondname, \'\')
                )) AS userName,
                ed.isSynthetic,
                ed.activeSince,
                es.s1, es.t1,
                es.s2, es.t2,
                es.s3, es.t3,
                es.s4, es.t4,
                es.s5, es.t5,
                es.s6, es.t6,
                es.s7, es.t7,
                es.isIrregular
            FROM
                OrgBundle:EmployeeToDepartment AS ed
                INNER JOIN OrgBundle:EmploymentHistory AS eh
                    WITH ed.employeeUserId = eh.employeeUserId
                        AND (eh.firedAt IS NULL OR eh.firedAt >= CURRENT_TIMESTAMP())
                LEFT JOIN OrgBundle:Employee AS em
                    WITH ed.employeeUserId=em.userId
                LEFT JOIN AppBundle:User AS uu
                    WITH ed.employeeUserId = uu.id
                LEFT JOIN AppBundle:Person AS pp
                    WITH uu.personId = pp.id
                LEFT JOIN OrgBundle:EmployeeSchedule AS es
                    WITH ed.employeeUserId = es.employeeUserId
                        AND es.activeSince <= CURRENT_TIMESTAMP()
                        AND (es.activeTill IS NULL OR es.activeTill >= CURRENT_TIMESTAMP())
            WHERE
                (ed.activeSince IS NULL OR ed.activeSince <= CURRENT_TIMESTAMP())
                AND (ed.activeTill IS NULL OR ed.activeTill >= CURRENT_TIMESTAMP())
            ORDER BY ed.departmentId, em.position, em.sortOrder, userName
        ');

        $employeesArray = $q->getResult();

        /** @var DTO\Employee[] $employees */
        $employees = [];

        $weekDay = date('w');
        if ($weekDay < 1)
            $weekDay = 7;

        $depId = -100;
        $depOrd = 1;
        $isChanged = false;

        foreach ($employeesArray AS $employeeInfo) {
            /** @var Employee $employee */
            $employee = $employeeInfo['employee'];

            /** @var DTO\Employee $employeeDto */
            $employeeDto = null;
            if (!isset($employees[$employee->getUserId()])) {
                $workSince = $employeeInfo["s$weekDay"];
                $workTill  = $employeeInfo["t$weekDay"];
                $employeeDto =
                    new DTO\Employee(
                        $employee->getUserId(),
                        $employeeInfo['userName'],
                        $employee->getSortOrder(),
                        $employee->getPosition(),
                        (empty($employeeInfo['activeSince']) ? false : true),
                        $employee->getClockInTime(),
                        $employeeInfo['isIrregular'] ?? false,
                        $workSince, $workTill
                    );
                $employees[$employee->getUserId()] = $employeeDto;
            } else {
                $employeeDto = $employees[$employee->getUserId()];
            }

            if ($depId != $employeeInfo['departmentId']) {
                $depId = $employeeInfo['departmentId'];
                $depOrd = 1;
            } else {
                ++$depOrd;
            }

            if ($depOrd == 1) {
                if (($employee->getPosition() == Employee::POSITION_CHIEF) || ($employee->getSortOrder() == $depOrd)) {
                    if (($employee->getPosition() != Employee::POSITION_CHIEF) || ($employee->getSortOrder() != $depOrd)) {
                        $employee->setPosition($employeeDto->position = Employee::POSITION_CHIEF);
                        $employee->setSortOrder($employeeDto->sortOrder = $depOrd);
                        $isChanged = true;
                    }
                } else {
                    $depOrd = 2;
                }
            }
            if (($depOrd == 2) && (($employee->getPosition() != Employee::POSITION_DEPUTY) || ($employee->getSortOrder() != $depOrd))) {
                $employee->setPosition($employeeDto->position = Employee::POSITION_DEPUTY);
                $employee->setSortOrder($employeeDto->sortOrder = $depOrd);
                $isChanged = true;
            }
            if (($depOrd > 2) && (($employee->getPosition() != Employee::POSITION_EXECUTIVE) || ($employee->getSortOrder() != $depOrd))) {
                $employee->setPosition($employeeDto->position = Employee::POSITION_EXECUTIVE);
                $employee->setSortOrder($employeeDto->sortOrder = $depOrd);
                $isChanged = true;
            }

            if ($isChanged)
                $em->persist($employee);

            if (isset($departments[$employeeInfo['departmentId']])) {
                /** @var DTO\Department $department */
                $department = $departments[$employeeInfo['departmentId']];

                if ($employee->getPosition() == Employee::POSITION_CHIEF) {
                    $department->chiefId = $employee->getUserId();
                    if ($employeeInfo['isSynthetic'])
                        $department->isInterimChief = true;
                } elseif ($employee->getPosition() == Employee::POSITION_DEPUTY) {
                    $department->deputyId = $employee->getUserId();
                } else {
                    $department->employeesIds[] = $employee->getUserId();
                }
            }
        }

        if ($isChanged)
            $em->flush();

        return ['departments' => array_values($departments), 'employees' => array_values($employees)];
    }
}
