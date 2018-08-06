<?php 

namespace OrgBundle\Bus\Employee\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\Activity;
use OrgBundle\Entity\ActivityArea;
use OrgBundle\Entity\Department;
use OrgBundle\Entity\DepartmentTypeEmployeeActivity;
use OrgBundle\Entity\Employee;
use OrgBundle\Entity\EmployeeSalaryActivity;
use OrgBundle\Entity\EmployeeToDepartment;
use OrgBundle\Entity\EmploymentHistory;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Validator\Exception\RuntimeException;

class CreateCommandHandler extends MessageHandler
{
    /**
     * @param CreateCommand $command
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(CreateCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();


        $numberParts = [];
        if (!preg_match('/^((?:\d+\.)*)(\d+)$/', $command->number, $numberParts))
            throw new InvalidParameterException('Invalid format of number');

        $numberDep = trim($numberParts[1], " \t\r\n.");
        $numberEmp = $numberParts[2] ?? 0;


        $queryDep = '
            SELECT d
            FROM OrgBundle:Department AS d
                INNER JOIN OrgBundle:DepartmentToDepartment AS dd
                    WITH d.id = dd.departmentId AND (d.pid = dd.pid OR d.pid IS NULL AND dd.pid IS NULL)
                        AND (dd.activeSince IS NULL OR dd.activeSince <= CURRENT_TIMESTAMP())
                        AND (dd.activeTill IS NULL OR dd.activeTill >= CURRENT_TIMESTAMP())
            WHERE d.number';
        if ($numberDep) {
            $queryDep .= "='$numberDep'";
        } else {
            $queryDep .= ' IS NULL';
        }
        /** @var Department[] $department */
        $department = $em->createQuery($queryDep)->getResult();

        if (count($department) != 1)
            throw new EntityNotFoundException('Department not found');

        $department = $department[0];


        /** @var User $user */
        $user = $em->getRepository(User::class)->find($command->userId);
        if (!$user)
            throw new EntityNotFoundException("User not found");


        $employee = $em->getRepository(Employee::class)->findBy(['userId' => $user->getId()]);
        if ($employee)
            throw new RuntimeException("Employee exists");


        $employee = new Employee();
        $employee->setUserId($user->getId());
        $employee->setSortOrder(($numberEmp < 1) ? 10000 : $numberEmp);


        /** @var Employee[] $colleagues */
        $colleagues = $em->createQuery('
                SELECT e
                FROM OrgBundle:Employee AS e
                    INNER JOIN OrgBundle:EmployeeToDepartment AS ed
                        WITH e.userId=ed.employeeUserId
                    INNER JOIN OrgBundle:EmploymentHistory AS eh
                        WITH ed.employeeUserId = eh.employeeUserId
                            AND (eh.firedAt IS NULL OR eh.firedAt >= CURRENT_TIMESTAMP())
                WHERE
                    ed.departmentId = :department_id
                    AND (ed.activeSince IS NULL OR ed.activeSince <= CURRENT_TIMESTAMP())
                    AND (ed.activeTill IS NULL OR ed.activeTill >= CURRENT_TIMESTAMP())
                ORDER BY e.sortOrder
            ')
            ->setParameter('department_id', $department->getId())
            ->getResult();

        $isUsedOrder = false;
        $freeOrder = 1;
        foreach ($colleagues as $colleague) {
            if ($colleague->getUserId() == $employee->getUserId())
                continue;

            if ($colleague->getSortOrder() == $employee->getSortOrder())
                $isUsedOrder = true;

            if ($isUsedOrder && ($colleague->getSortOrder() >= $employee->getSortOrder())) {
                $colleague->setSortOrder($colleague->getSortOrder() + 1);

                $colleague->setPosition(($colleague->getSortOrder() == 2) ? Employee::POSITION_DEPUTY : Employee::POSITION_EXECUTIVE);
            }

            $freeOrder = $colleague->getSortOrder();
        }

        if (!$isUsedOrder && ($employee->getSortOrder() > 1)) {
            $employee->setSortOrder($freeOrder + 1);
        }
        $employee->setPosition(($employee->getSortOrder() == 1) ? Employee::POSITION_CHIEF : (($employee->getSortOrder() == 2) ? Employee::POSITION_DEPUTY : Employee::POSITION_EXECUTIVE));
        $em->persist($employee);

        $empInDep = new EmployeeToDepartment();
        $empInDep->setEmployeeUserId($employee->getUserId());
        $empInDep->setDepartmentId($department->getId());
        $empInDep->setActivatedBy($currentUser->getId());
        $empInDep->setIsSynthetic(false);
        $em->persist($empInDep);

        $employeeHistory = new EmploymentHistory();
        $employeeHistory->setEmployeeUserId($employee->getUserId());
        $employeeHistory->setHiredAt(new \DateTime());
        $em->persist($employeeHistory);


        if ($department->getTypeCode()) {

            $userNameArr = [];
            if ($user->getPerson()->getLastname())
                $userNameArr[] = $user->getPerson()->getLastname();
            if ($user->getPerson()->getFirstname())
                $userNameArr[] = $user->getPerson()->getFirstname();
            if ($user->getPerson()->getSecondname())
                $userNameArr[] = $user->getPerson()->getSecondname();
            $userName = implode(' ', $userNameArr);


            /** @var Activity[] $activitiesRes */
            $activitiesRes = $em->createQuery('
                        SELECT
                            a,
                            aa,
                            ai,
                            ao
                        FROM OrgBundle:Activity AS a
                            LEFT JOIN a.activityIndex AS ai
                            LEFT JOIN a.activityObject AS ao
                            LEFT JOIN a.activityArea AS aa
                        WHERE a.activityAreaValue = :employeeId
                        ORDER BY a.name, a.id
                    ')
                ->setParameter('employeeId', $employee->getUserId())
                ->getResult();

            /** @var Activity[] $activities */
            $activities = [];

            foreach ($activitiesRes as $activity) {
                if ($activity->getDepartmentTypeActivityId() && ($activity->getActivityArea()->getCode() == ActivityArea::CODE_EMPLOYEE)) {
                    $activities[$activity->getDepartmentTypeActivityId()] = $activity;
                }
            }


            /** @var DepartmentTypeEmployeeActivity[] $departmentTypeActivitiesRes */
            $departmentTypeActivitiesRes = $em->createQuery('
                        SELECT
                            dtea,
                            aa,
                            ai,
                            ao
                        FROM OrgBundle:DepartmentTypeEmployeeActivity AS dtea
                            LEFT JOIN dtea.activityIndex AS ai
                            LEFT JOIN dtea.activityObject AS ao
                            LEFT JOIN dtea.activityArea AS aa
                        WHERE dtea.departmentTypeCode = :departmentType
                    ')
                ->setParameter('departmentType', $department->getTypeCode())
                ->getResult();

            /** @var Activity[] $departmentActivities */
            $departmentActivities = [];

            foreach ($departmentTypeActivitiesRes as $departmentTypeActivity) {
                if (($departmentTypeActivity->getActivityArea()->getCode() == ActivityArea::CODE_EMPLOYEE)
                    && (($departmentTypeActivity->getIsChief() && ($employee->getPosition() == Employee::POSITION_CHIEF))
                        || (!$departmentTypeActivity->getIsChief() && ($employee->getPosition() != Employee::POSITION_CHIEF)))) {

                    if (!isset($activities[$departmentTypeActivity->getId()])) {

                        $activity = new Activity();
                        $activity->setDepartmentTypeActivityId($departmentTypeActivity->getId());
                        $activity->setActivityIndex($departmentTypeActivity->getActivityIndex());
                        $activity->setActivityObject($departmentTypeActivity->getActivityObject());
                        $activity->setActivityArea($departmentTypeActivity->getActivityArea());
                        $activity->setActivityAreaValue($employee->getUserId());
                        $activity->setCategoryId($departmentTypeActivity->getCategoryId());
                        $activity->setIntervalMonth($departmentTypeActivity->getIntervalMonth());
                        $em->persist($activity);

                        $em->flush();

                        $employeeActivity = null;

                    } else {
                        $activity = $activities[$departmentTypeActivity->getId()];

                        $employeeActivity = $em->getRepository(EmployeeSalaryActivity::class)
                                ->findOneBy(['activityId' => $activity->getId()]);
                    }

                    if (!$employeeActivity) {
                        $employeeActivity = new EmployeeSalaryActivity();
                        $employeeActivity->setEmployeeUserId($employee->getUserId());
                        $employeeActivity->setActivityId($activity->getId());
                        $employeeActivity->setActiveSince(new \DateTime(date('Y-m-01')));
                        $employeeActivity->setIsPlaned($departmentTypeActivity->getIsPlanned());
                        $employeeActivity->setCoefficient($departmentTypeActivity->getCoefficient());
                        $employeeActivity->setRate($departmentTypeActivity->getRate());
                        $em->persist($activity);
                    }

                    $activity->setName($departmentTypeActivity->getName() . ' ' . $userName);
                    $departmentActivities[$departmentTypeActivity->getId()] = $activity;
                }
            }

            foreach ($activities as $activity) {
                if (!isset($departmentActivities[$activity->getDepartmentTypeActivityId()])) {
                    $em->remove($activity);
                }
            }


            if ($employee->getPosition() == Employee::POSITION_CHIEF) {

                /** @var Activity[] $activitiesRes */
                $activitiesRes = $em->createQuery('
                        SELECT
                            a,
                            aa,
                            ai,
                            ao
                        FROM OrgBundle:Activity AS a
                            LEFT JOIN a.activityIndex AS ai
                            LEFT JOIN a.activityObject AS ao
                            LEFT JOIN a.activityArea AS aa
                        WHERE a.activityAreaValue = :departmentId
                        ORDER BY a.name, a.id
                    ')
                    ->setParameter('departmentId', $department->getId())
                    ->getResult();

                /** @var Activity[] $activities */
                $activities = [];

                foreach ($activitiesRes as $activity) {
                    if ($activity->getDepartmentTypeActivityId()
                        && in_array($activity->getActivityArea()->getCode(),
                            [ActivityArea::CODE_DEPARTMENT, ActivityArea::CODE_AREA, ActivityArea::CODE_POINT])) {
                        $activities[$activity->getDepartmentTypeActivityId()] = $activity;
                    }
                }


                /** @var Activity[] $departmentActivities */
                $departmentActivities = [];

                foreach ($departmentTypeActivitiesRes as $departmentTypeActivity) {
                    if (in_array($departmentTypeActivity->getActivityArea()->getCode(),
                        [ActivityArea::CODE_DEPARTMENT, ActivityArea::CODE_AREA, ActivityArea::CODE_POINT])) {
                        if (!isset($activities[$departmentTypeActivity->getId()])) {

                            $activity = new Activity();
                            $activity->setDepartmentTypeActivityId($departmentTypeActivity->getId());
                            $activity->setActivityIndex($departmentTypeActivity->getActivityIndex());
                            $activity->setActivityObject($departmentTypeActivity->getActivityObject());
                            $activity->setActivityArea($departmentTypeActivity->getActivityArea());
                            $activity->setActivityAreaValue($department->getId());
                            $activity->setCategoryId($departmentTypeActivity->getCategoryId());
                            $activity->setIntervalMonth($departmentTypeActivity->getIntervalMonth());
                            $em->persist($activity);

                            $em->flush();

                            $employeeActivity = null;

                        } else {
                            $activity = $activities[$departmentTypeActivity->getId()];

                            $employeeActivity = $em->getRepository(EmployeeSalaryActivity::class)
                                ->findOneBy(['activityId' => $activity->getId()]);
                        }

                        if (!$employeeActivity) {
                            $employeeActivity = new EmployeeSalaryActivity();
                            $employeeActivity->setEmployeeUserId($employee->getUserId());
                            $employeeActivity->setActivityId($activity->getId());
                            $employeeActivity->setActiveSince(new \DateTime(date('Y-m-01')));
                            $employeeActivity->setIsPlaned($departmentTypeActivity->getIsPlanned());
                            $employeeActivity->setCoefficient($departmentTypeActivity->getCoefficient());
                            $employeeActivity->setRate($departmentTypeActivity->getRate());
                            $em->persist($activity);
                        } elseif ($employeeActivity->getEmployeeUserId() != $employee->getUserId()) {
                            $employeeActivity->setEmployeeUserId($employee->getUserId());
                        }

                        $activity->setName($departmentTypeActivity->getName() . ' ' . $department->getName());
                        $departmentActivities[$departmentTypeActivity->getId()] = $activity;
                    }
                }

                foreach ($activities as $activity) {
                    if (!isset($departmentActivities[$activity->getDepartmentTypeActivityId()])) {
                        $em->remove($activity);
                    }
                }
            }
        }

        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $employee->getUserId());
    }
}