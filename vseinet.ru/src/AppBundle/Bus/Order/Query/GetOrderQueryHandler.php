<?php

namespace AppBundle\Bus\Order\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Entity\FinancialCounteragent;

class GetOrderQueryHandler extends MessageHandler
{
    public function handle(GetOrderQuery $query)
    {
        $api = $this->get('site.api.client');

        try {
            $parameters = [
                'did' => $query->id,
            ];
            $result = $api->get('/api/v1/orders/?'.http_build_query($parameters));
        } catch (BadRequestHttpException $e) {
            return null;
        }

        if (0 === $result['total']) {
            return null;
        }

        try {
            $items = $api->get('/api/v1/orderItems/?orderIds[]='.$query->id);
        } catch (BadRequestHttpException $e) {
            return null;
        }

        $order = reset($result['items']);
        $order['items'] = $items;

        return new DTO\Order($order);
    }
}
