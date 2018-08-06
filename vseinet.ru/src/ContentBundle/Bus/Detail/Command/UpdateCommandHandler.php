<?php

namespace ContentBundle\Bus\Detail\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Detail;
use ContentBundle\Entity\DetailGroup;
use ContentBundle\Entity\DetailType;
use ContentBundle\Entity\MeasureUnit;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $detail = $em->getRepository(Detail::class)->find($command->id);
        if (!$detail instanceof Detail) {
            throw new NotFoundHttpException(sprintf('Характеристика %d не найдена', $command->id));
        }

        $oldTypeCode = $detail->getTypeCode();
        $oldUnitId = $detail->getUnitId();
        
        if ($detail->getGroupId() != $command->groupId) {
            $group = $em->getRepository(DetailGroup::class)->find($command->groupId);
            if (!$group instanceof DetailGroup) {
                throw new NotFoundHttpException(sprintf('Группа характеристик %d не найдена', $command->groupId));
            }
            $detail->setGroupId($group->getId());
            $detail->setSortOrder($this->getMaxSortOrder($group) + 1);
        }

        $detail->setName($command->name);

        if ($detail->getTypeCode() != $command->typeCode) {
            $type = $em->getRepository(DetailType::class)->find($command->typeCode);
            if (!$type instanceof DetailType) {
                throw new NotFoundHttpException(sprintf('Тип характеристики %s не найден', $command->typeCode));
            }
            $detail->setTypeCode($type->getCode());
        }
        else {
            $type = $em->getRepository(DetailType::class)->find($detail->getTypeCode());
        }
        if ($type->getCanBeMeasured()) {
            if ($detail->getUnitId() != $command->unitId) {
                $unit = $em->getRepository(MeasureUnit::class)->find($command->unitId);
                if (!$unit instanceof MeasureUnit) {
                    throw new NotFoundHttpException(sprintf('Еденица измерения %d не найдена', $command->unitId));
                }
                $detail->setUnitId($unit->getId());
            }
        }
        else {
            $detail->setUnitId(null);
        }

        $em->persist($detail);
        $em->flush();

        if ($detail->getTypeCode() != $oldTypeCode || $detail->getUnitId() != $oldUnitId) {
            $this->get('command_bus')->handle(new ConvertCommand([
                'id' => $detail->getId(),
                'newTypeCode' => $detail->getTypeCode(),
                'oldTypeCode' => $oldTypeCode,
                'newUnitId' => $detail->getUnitId(),
                'oldUnitId' => $oldUnitId,
            ]));   
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