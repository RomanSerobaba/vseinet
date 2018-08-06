<?php

namespace ContentBundle\Bus\Detail\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Detail;
use ContentBundle\Entity\DetailGroup;
use ContentBundle\Entity\DetailType;
use ContentBundle\Entity\MeasureUnit;
use ContentBundle\Entity\CategorySection;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository(DetailGroup::class)->find($command->groupId);
        if (!$group instanceof DetailGroup) {
            throw new NotFoundHttpException(sprintf('Группа характеристик %d не найдена', $command->groupId));
        }

        if (null !== $command->sectionId) {
            $section = $em->getRepository(CategorySection::class)->find($command->sectionId);
            if (!$section instanceof CategorySection) {
                throw new NotFoundHttpException(sprintf('Раздел категории %d не найден', $command->sectionId));
            }
        }

        $type = $em->getRepository(DetailType::class)->find($command->typeCode);
        if (!$type instanceof DetailType) {
            throw new NotFoundHttpException(sprintf('Тип характеристики %s не найден', $command->typeCode));
        }

        if (null !== $command->unitId) {
            $unit = $em->getRepository(MeasureUnit::class)->find($command->unitId);
            if (!$unit instanceof MeasureUnit) {
                throw new NotFoundHttpException(sprintf('Еденица измерения %d не найдена', $command->unitId));
            }
        }

        $detail = new Detail();
        $detail->setGroupId($command->groupId);
        $detail->setSectionId($command->sectionId);
        $detail->setName($command->name);
        $detail->setTypeCode($command->typeCode);
        if ($type->getCanBeMeasured()) {
            $detail->setUnitId($command->unitId);
        }
        $detail->setSortOrder($this->getMaxSortOrder($group) + 1);

        $em->persist($detail);
        $em->flush();

        if ($type->getIsComposite()) {
            $this->get('command_bus')->handle(new CreateDependCommand(json_decode($this->serialize($detail), true)));
        }
    }

    protected function getMaxSortOrder(DetailGroup $group)
    {
        $query = $this->getDoctrine()->getManager()->createQuery("
            SELECT MAX(d.sortOrder)
            FROM ContentBundle:Detail d  
            WHERE d.groupId = :groupId 
        ");
        $query->setParameter('groupId', $group->getId());

        try {
            return $query->getSingleScalarResult();
        } 
        catch (NoResultException $e) {
            return 0;
        }
    }
}