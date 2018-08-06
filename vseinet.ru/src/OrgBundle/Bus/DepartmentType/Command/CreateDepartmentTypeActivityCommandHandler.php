<?php 

namespace OrgBundle\Bus\DepartmentType\Command;

use AppBundle\Bus\Message\MessageHandler;
use ContentBundle\Entity\Category;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\ActivityArea;
use OrgBundle\Entity\ActivityIndex;
use OrgBundle\Entity\ActivityObject;
use OrgBundle\Entity\DepartmentType;
use OrgBundle\Entity\DepartmentTypeEmployeeActivity;

class CreateDepartmentTypeActivityCommandHandler extends MessageHandler
{
    /**
     * @param CreateDepartmentTypeActivityCommand $command
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(CreateDepartmentTypeActivityCommand $command)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var DepartmentType $departmentType */
        $departmentType = $em->getRepository(DepartmentType::class)->find($command->departmentTypeId);

        if (!$departmentType)
            throw new EntityNotFoundException('Нет такого типа подразделения');


        /** @var ActivityObject $object */
        $object = $em->getRepository(ActivityObject::class)->find($command->activityObjectId);

        if (!$object)
            throw new EntityNotFoundException('Нет такого объекта измерений показателей');


        /** @var ActivityIndex[] $indexesRes */
        $indexesRes = $em->createQuery('
                SELECT
                    ai
                FROM OrgBundle:ActivityIndex AS ai
                    INNER JOIN OrgBundle:ActivityObjectToIndex AS aoi
                        WITH ai.id = aoi.activityIndexId
                WHERE aoi.activityObjectId = :objectId
                ORDER BY ai.name
            ')
            ->setParameter('objectId', $object->getId())
            ->getResult();

        /** @var ActivityIndex[] $indexes */
        $indexes = [];
        foreach ($indexesRes as $index) {
            $indexes[$index->getId()] = $index;
        }


        /** @var ActivityArea[] $areasRes */
        $areasRes = $em->createQuery('
                SELECT
                    aa
                FROM OrgBundle:ActivityArea AS aa
                    INNER JOIN OrgBundle:ActivityObjectToArea AS aoa
                        WITH aa.id = aoa.activityAreaId
                WHERE aoa.activityObjectId = :objectId
                ORDER BY aa.name
            ')
            ->setParameter('objectId', $object->getId())
            ->getResult();

        /** @var ActivityArea[] $areas */
        $areas = [];
        foreach ($areasRes as $area) {
            $areas[$area->getId()] = $area;
        }


        $activity = new DepartmentTypeEmployeeActivity();
        $activity->setDepartmentTypeCode($departmentType->getCode());
        $activity->setIsChief($command->isChief ? true : false);
        $activity->setIsDepartment($command->isDepartment ? true : false);
        $activity->setIsPlanned($command->isPlanned ? true : false);

        $name = '';

        if (isset($indexes[$command->activityIndexId])) {
            $activity->setActivityIndex($indexes[$command->activityIndexId]);
            $name .= $indexes[$command->activityIndexId]->getName() . ' ';
        }

        $activity->setActivityObject($object);
        $name .= $object->getName();

        if (isset($areas[$command->activityAreaId])) {
            $activity->setActivityArea($areas[$command->activityAreaId]);
            $name .= ' ' . $areas[$command->activityAreaId]->getName();
        }

        if ($command->categoryId) {
            /** @var Category $category */
            $category = $em->getRepository(Category::class)->find($command->categoryId);

            if ($category) {
                $activity->setCategoryId($category->getId());
                $name .= ' по категории ' . $category->getName();
            }
        }

        $activity->setName($command->name ? $command->name : $name);

        $activity->setIntervalMonth($command->intervalMonth);
        $activity->setCoefficient($command->coefficient);
        $activity->setRate($command->rate);

        $em->persist($activity);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $activity->getId());
    }
}