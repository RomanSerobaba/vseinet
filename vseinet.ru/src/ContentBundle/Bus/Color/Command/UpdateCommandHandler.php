<?php

namespace ContentBundle\Bus\Color\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\Color;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command) 
    {
        if (!preg_match('/[0-9a-fA-F]{3,6}/', $command->valueHex)) {
            throw new BadRequestHttpException('Неверный шестнадцатеричный код');
        }

        $em = $this->getDoctrine()->getManager();

        $color = $em->getRepository(Color::class)->find($command->id);
        if (!$color instanceof Color) {
            throw new NotFoundHttpException('Цвет не найден');
        }

        $color->setValueDec(hexdec($command->valueHex));
        $color->setValueHex(strtoupper($command->valueHex));
        $color->setPaletteId($command->paletteId);
        $color->setNameMale($command->nameMale);
        $color->setNameFemale($command->nameFemale);
        $color->setNameNeuter($command->nameNeuter);
        $color->setNameAblative($command->nameAblative);
        $color->setNamePlural($command->namePlural);
        $color->setIsBase($command->isBase);

        $em->persist($color);
        $em->flush();
    }
}