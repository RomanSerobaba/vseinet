<?php

namespace OrgBundle\Bus\Department\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\Activity;
use OrgBundle\Entity\ActivityArea;
use OrgBundle\Entity\Department;
use OrgBundle\Entity\DepartmentTypeEmployeeActivity;

class UpdateDepartmentInfoCommandHandler extends MessageHandler
{
    /**
     * @param UpdateDepartmentInfoCommand $command
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(UpdateDepartmentInfoCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /** @var Department $department */
        $department = $em->getRepository(Department::class)->find($command->id);

        if (!$department) {
            throw new EntityNotFoundException('Подразделение не найдено');
        }


        if ($command->typeCode && ($department->getTypeCode() != $command->typeCode)) {

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
                        $em->persist($activity);

                    } else {
                        $activity = $activities[$departmentTypeActivity->getId()];
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


        $department->setName($command->name);
        $department->setTypeCode($command->typeCode);
        $department->setSalaryDay($command->salaryDay);
        $department->setSalaryPaymentType($command->salaryPaymentType);
        $department->setSalaryPaymentSource($command->salaryPaymentSource);
        $em->persist($department);

        $em->flush();
    }
}