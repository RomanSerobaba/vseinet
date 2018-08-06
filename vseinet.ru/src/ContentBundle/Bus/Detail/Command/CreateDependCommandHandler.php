<?php

namespace ContentBundle\Bus\Detail\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\Detail;
use AppBundle\Enum\DetailType;

/**
 * @internal
 */
class CreateDependCommandHandler extends MessageHandler
{
    public function handle(CreateDependCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        switch ($command->typeCode) {
            case 'dimensions':
                $names = ['Ширина', 'Высота', 'Глубина'];
                break;

            case 'size':
                $names = ['Ширина', 'Длина'];
                break;

            case 'range':
                $names = ['От', 'До'];
                break;

            default:
                throw new BadRequestHttpException(sprintf('Не верный тип составной характеристики %s, должно быть dimensions, size или range', $command->typeCode));
        }

        $sortOrder = 0;
        foreach ($names as $name) {
            $depend = new Detail();
            $depend->setPid($command->id);
            $depend->setGroupId($command->groupId);
            $depend->setSectionId($command->sectionId);
            $depend->setName($name);
            $depend->setTypeCode(DetailType::CODE_NUMBER);
            $depend->setUnitId($command->unitId);
            $depend->setSortOrder(++$sortOrder);

            $em->persist($depend);
        }
        $em->flush();
    }
}