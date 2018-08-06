<?php 

namespace ContentBundle\Bus\ColorPalette\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetAllQueryHandler extends MessageHandler
{
    public function handle(GetAllQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\ColorPalette\Query\DTO\ColorPalette (
                    cp.id,
                    cp.name 
                )
            FROM ContentBundle:ColorPalette cp 
            ORDER BY cp.sortOrder 
        ");
        $palettes = $q->getResult('IndexByHydrator');

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\ColorPalette\Query\DTO\Color (
                    c.id,
                    c.paletteId,
                    c.valueHex,
                    c.nameMale,
                    c.nameFemale,
                    c.nameNeuter,
                    c.namePlural,
                    c.nameAblative,
                    c.isBase
                )
            FROM ContentBundle:Color c 
            ORDER BY c.sortOrder 
        ");
        $colors = $q->getArrayResult();
        foreach ($colors as $color) {
            $palettes[$color->paletteId]->colorIds[] = $color->id;
        }

        return new DTO\ColorPalettes($palettes, $colors);
    }
}