<?php

namespace AppBundle\Bus\Order\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Exception\ValidationException;
use AppBundle\Entity\ClientOrder;
use AppBundle\Entity\OrderDoc;

class GetStatusQueryHandler extends MessageHandler
{
    public function handle(GetStatusQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository(OrderDoc::class)->findOneByNumber($query->number);
        if (!$order instanceof OrderDoc) {
            throw new ValidationException('number', 'Заказ не найден');
        }
        $client = $em->getRepository(ClientOrder::class)->find($order->getDid());
        if (!$client instanceof ClientOrder) {
            throw new ValidationException('number', 'Заказ не найден');
        }

        $api = $this->get('site.api.client');

        try {
            $items = $api->get('/v1/orderItems/?orderIds[]='.$order->getDId());
        } catch (BadRequestHttpException $e) {
            throw new ValidationException('number', $e->getMessage());
        }

        return new DTO\Order([
            'id' => $order->getDId(),
            'number' => $order->getNumber(),
            'createdAt' => $order->getCreatedAt(),
            'items' => $items,
        ]);
    }
}
