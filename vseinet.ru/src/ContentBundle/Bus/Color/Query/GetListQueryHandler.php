<?php 

namespace ContentBundle\Bus\Color\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ColorPalette;
use ContentBundle\Entity\Color;

/**
 * @deprecated
 */
class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $palette = $em->getRepository(ColorPalette::class)->find($query->paletteId);
        if (!$palette instanceof ColorPalette) {
            throw new NotFoundHttpException('Цветовая палитра не найдена');
        }

        return $em->getRepository(Color::class)->findBy(['paletteId' => $palette->getId()], ['sortOrder' => 'ASC']);
    }
}