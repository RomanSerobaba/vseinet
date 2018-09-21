<?php 

namespace AppBundle\Bus\Order\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Bus\Exception\ValidationException;
use AppBundle\Entity\ClientOrder;
use AppBundle\Entity\OrderDoc;

class GetStatusQueryHandler extends MessageHandler
{
    public function handle(GetStatusQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository(ClientOrder::class)->find($query->number);
        if (!$order instanceof ClientOrder) {
            throw new ValidationException([
                'number' => 'Заказ не найден',
            ]);
        }

        $api = $this->get('site.api.client');
        try {
            $items = $api->get('/api/v1/orderItems/?orderIds[]='.$query->number);
        } catch (BadRequestHttpException $e) {
            throw new ValidationException([
                'number' => $e->getMessage(),
            ]);
        }

        $doc = $em->getRepository(OrderDoc::class)->findOneBy(['number' => $query->number]);

        return new DTO\Order([
            'id' => $order->getId(), 
            'createdAt' => $doc->getCreatedAt(), 
            'items' => $items,
        ]);
    }
}
