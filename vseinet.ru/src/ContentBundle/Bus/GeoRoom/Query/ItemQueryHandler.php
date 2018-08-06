<?php 

namespace ContentBundle\Bus\GeoRoom\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\GeoRoom;

class ItemQueryHandler extends MessageHandler
{
    public function handle(ItemQuery $query)
    {
        $item = $this->getDoctrine()->getManager()->getRepository(GeoRoom::class)->find($query->id);
        if (!$item instanceof GeoRoom) {
            throw new NotFoundHttpException('Элемент не найден');
        }

        return $item;
    }

}
