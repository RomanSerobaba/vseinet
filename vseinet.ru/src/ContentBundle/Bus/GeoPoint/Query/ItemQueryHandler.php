<?php 

namespace ContentBundle\Bus\GeoPoint\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\GeoPoint;

class ItemQueryHandler extends MessageHandler
{
    public function handle(ItemQuery $query)
    {
        $item = $this->getDoctrine()->getManager()->getRepository(GeoPoint::class)->find($query->id);
        if (!$item instanceof GeoPoint) {
            throw new NotFoundHttpException('Элемент не найден');
        }

        return $item;
    }

}
