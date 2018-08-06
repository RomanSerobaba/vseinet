<?php 

namespace ContentBundle\Bus\Supplier\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SupplyBundle\Entity\Supplier;

class GetItemQueryHandler extends MessageHandler
{
    public function handle(GetItemQuery $query)
    {
        $item = $this->getDoctrine()->getManager()->getRepository(Supplier::class)->find($query->id);
        if (!$item instanceof Supplier) {
            throw new NotFoundHttpException('Поставщик не найден');
        }

        return $item;
    }
}