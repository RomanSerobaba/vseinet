<?php

namespace ContentBundle\Bus\Color\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Color;
use ContentBundle\Entity\ColorPalette;
use Doctrine\ORM\NoResultException;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        if (!preg_match('/[0-9a-fA-F]{3,6}/', $command->valueHex)) {
            throw new BadRequestHttpException('Неверный шестнадцатеричный код');
        }

        $em = $this->getDoctrine()->getManager();

        $palette = $em->getRepository(ColorPalette::class)->find($command->paletteId);
        if (!$palette instanceof ColorPalette) {
            throw new NotFoundHttpException('Цветовая палитра не найдена');
        }

        $color = new Color();
        $color->setValueDec(hexdec($command->valueHex));
        $color->setValueHex(strtoupper($command->valueHex));
        $color->setPaletteId($palette->getId());
        $color->setNameMale($command->nameMale);
        $color->setNameFemale($command->nameFemale);
        $color->setNameNeuter($command->nameNeuter);
        $color->setNameAblative($command->nameAblative);
        $color->setNamePlural($command->namePlural);
        $color->setIsBase($command->isBase);
        $color->setSortOrder($this->getMaxSortOrder($palette) + 1);

        $em->persist($color);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $color->getId());
    }

    protected function getMaxSortOrder(ColorPalette $palette)
    {
        $query = $this->getDoctrine()->getManager()->createQuery("
            SELECT MAX(c.sortOrder)
            FROM ContentBundle:Color c 
            WHERE c.paletteId = :paletteId 
        ");
        $query->setParameter('paletteId', $palette->getId());

        try {
            return $query->getSingleScalarResult();
        } 
        catch (NoResultException $e) {
            return 0;
        }
    }
}