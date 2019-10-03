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
            $result = $api->get('/api/v1/orders/'.$order->getDId().'/');
        } catch (BadRequestHttpException $e) {
            throw new ValidationException('number', $e->getMessage());
        }

        $items = [];

        foreach ($result['orderItems'] as $item) {
            foreach ($item['statuses'] as $status) {
                $items[] = array_merge($item, ['statusCode' => $status['code'], 'statusCodeName' => $status['clientName']]);
            }
        }

        return new DTO\Order([
            'id' => $order->getDId(),
            'number' => $order->getNumber(),
            'createdAt' => $order->getCreatedAt(),
            'paymentTypeCode' => $result['order']['paymentTypeCode'],
            'prepaymentAmount' => $result['order']['prepaymentAmount'],
            'items' => $items,
        ]);
    }
}
