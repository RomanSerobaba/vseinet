<?php 

namespace ContentBundle\Bus\GeoCity\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\GeoCity;

class ItemQueryHandler extends MessageHandler
{
    public function handle(ItemQuery $query)
    {
        $item = $this->getDoctrine()->getManager()->getRepository(GeoCity::class)->find($query->id);
        if (!$item instanceof GeoCity) {
            throw new NotFoundHttpException('Элемент не найден');
        }

        return $item;
    }

}
