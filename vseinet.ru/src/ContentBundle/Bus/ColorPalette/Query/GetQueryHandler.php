<?php 

namespace ContentBundle\Bus\ColorPalette\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ColorPalette;
use ContentBundle\Entity\Color;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $palette = $em->getRepository(ColorPalette::class)->find($query->id);
        if (!$palette instanceof ColorPalette) {
            throw new NotFoundHttpException(sprintf('Цветовая палитра %d не найдена', $query->id));
        }

        if ($query->colors) {
            $palette->colors = $em->getRepository(Color::class)->findBy(['paletteId' => $palette->getId()], ['sortOrder' => 'ASC']);
        }

        return $palette;
    }
}