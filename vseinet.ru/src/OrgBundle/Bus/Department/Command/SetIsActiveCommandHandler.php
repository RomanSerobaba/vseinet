<?php 

namespace OrgBundle\Bus\Department\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Query\ResultSetMapping;
use OrgBundle\Entity\Activity;
use OrgBundle\Entity\ActivityArea;
use OrgBundle\Entity\Department;
use OrgBundle\Entity\DepartmentToDepartment;
use OrgBundle\Entity\DepartmentTypeEmployeeActivity;
use OrgBundle\Entity\Employee;
use Symfony\Component\Validator\Exception\RuntimeException;

class SetIsActiveCommandHandler extends MessageHandler
{
    /**
     * @param SetIsActiveCommand $command
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(SetIsActiveCommand $command)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var User $currentUser */
        $currentUser = $this->get('user.identity')->getUser();


        /** @var Department $department */
        $department = $em->getRepository(Department::class)->find($command->id);

        if (!$department)
            throw new EntityNotFoundException('Department not found');


        if ($command->value) {

            /** @var DepartmentToDepartment[] $depToDep */
            $depToDep = $em->createQuery('
                    SELECT dd
                    FROM OrgBundle:DepartmentToDepartment AS dd
                    WHERE
                        dd.departmentId = :department_id AND dd.pid = :pid
                        AND (dd.activeSince IS NULL OR dd.activeSince <= CURRENT_TIMESTAMP())
                        AND (dd.activeTill IS NULL OR dd.activeTill >= CURRENT_TIMESTAMP())
                ')
                ->setParameter('department_id', $department->getId())
                ->setParameter('pid', $department->getPid())
                ->getResult();

            if (count($depToDep) > 0) {
                foreach ($depToDep as $dep) {
                    $dep->setActiveSince(new \DateTime());
                    $dep->setActivatedBy($currentUser->getId());
                    $em->persist($dep);
                }
            } else {
                $dep = new DepartmentToDepartment();
                $dep->setDepartmentId($department->getId());
                $dep->setPid($department->getPid());
                $dep->setActiveSince(new \DateTime());
                $dep->setActivatedBy($currentUser->getId());
                $em->persist($dep);
            }


            if ($department->getTypeCode()) {
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
                            $activity->setName($departmentTypeActivity->getName() . ' ' . $department->getName());

                            $em->persist($activity);
                            $departmentActivities[$departmentTypeActivity->getId()] = $activity;

                        } else {
                            $departmentActivities[$departmentTypeActivity->getId()] = $activities[$departmentTypeActivity->getId()];
                        }
                    }
                }

                foreach ($activities as $activity) {
                    if (!isset($departmentActivities[$activity->getDepartmentTypeActivityId()])) {
                        $em->remove($activity);
                    }
                }
            }

            $em->flush();

        } else {

            /** @var DepartmentToDepartment[] $depToDep */
            $depToDep = $em->createQuery('
                    SELECT dd
                    FROM OrgBundle:DepartmentToDepartment AS dd
                    WHERE
                        dd.pid = :department_id
                        AND (dd.activeSince IS NULL OR dd.activeSince <= CURRENT_TIMESTAMP())
                        AND (dd.activeTill IS NULL OR dd.activeTill >= CURRENT_TIMESTAMP())
                ')
                ->setParameter('department_id', $department->getId())
                ->getResult();

            if (count($depToDep) > 0)
                throw new RuntimeException('Impossible to deactivate department with departments');


            /** @var Employee[] $employees */
            $employees = $em->createQuery('
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

            if (count($employees) > 0)
                throw new RuntimeException('Impossible to deactivate department with employees');


            /** @var DepartmentToDepartment[] $depToDep */
            $depToDep = $em->createQuery('
                    SELECT dd
                    FROM OrgBundle:DepartmentToDepartment AS dd
                    WHERE
                        dd.departmentId = :department_id AND dd.pid = :pid
                        AND (dd.activeSince IS NULL OR dd.activeSince <= CURRENT_TIMESTAMP())
                        AND (dd.activeTill IS NULL OR dd.activeTill >= CURRENT_TIMESTAMP())
                ')
                ->setParameter('department_id', $department->getId())
                ->setParameter('pid', $department->getPid())
                ->getResult();

            foreach ($depToDep as $dep) {
                $dep->setActiveTill(new \DateTime());
            }


            if ($department->getTypeCode()) {
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

                foreach ($activitiesRes as $activity) {
                    if ($activity->getDepartmentTypeActivityId()
                        && in_array($activity->getActivityArea()->getCode(),
                                    [ActivityArea::CODE_DEPARTMENT, ActivityArea::CODE_AREA, ActivityArea::CODE_POINT])) {
                        $em->remove($activity);
                    }
                }
            }

            $em->flush();

            $em->createNativeQuery('SELECT department_update_number()', new ResultSetMapping())->execute();
        }
    }
}