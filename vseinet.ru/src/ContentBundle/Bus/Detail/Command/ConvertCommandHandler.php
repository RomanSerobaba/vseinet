<?php

namespace ContentBundle\Bus\Detail\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\Detail;
use ContentBundle\Entity\DetailType;
use ContentBundle\Entity\MeasureUnit;

class ConvertCommandHandler extends MessageHandler
{
    public function handle(ConvertCommand $command) 
    {
        return;
        
        $converter = $command->oldType.'2'.$command->newType;
        if (!method_exists($this, $converter)) {
            throw new BadRequestHttpException(sprintf('Конвертация характеристики из типа "%s" в тип "%s" не поддерживается', $command->oldType, $command->newType));
        }

        $detail = $this->getDoctrine()->getManager()->getRepository(Detail::class)->find($command->id);
        if (!$detail instanceof Detail) {
            throw new NotFoundHttpException(sprintf('Характеристика %d не найдена', $command->id));
        }
        if ($detail->getPid()) {
            throw new BadRequestHttpException('Конвертация дочерних характеристик не поддерживается');
        }

        if (null === $command->oldUnitId) {
            $oldUnit = null;
        }
        else {
            $oldUnit = $em->getRepository(MeasureUnit::class)->find($command->oldUnitId);
            if (!$oldUnit instanceof MeasureUnit) {
                throw new NotFoundHttpException(sprintf('Единица измерения %d не найдена', $command->oldUnitId));
            }
        }

        if (null === $command->newUnitId) {
            $newUnit = null;
        }
        else {
            $newUnit = $em->getRepository(MeasureUnit::class)->find($command->newUnitId);
            if (!$newUnit instanceof MeasureUnit) {
                throw new NotFoundHttpException(sprintf('Единица измерения %d не найдена', $command->newUnitId));
            }
        }

        $this->$converter($detail, $command->oldType, $command->newType, $oldUnit, $newUnit);
    }



}