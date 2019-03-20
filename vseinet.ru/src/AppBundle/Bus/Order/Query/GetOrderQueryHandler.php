<?php

namespace AppBundle\Bus\Order\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

        $order = reset($result['orders']);

        foreach ($result['orderItems'] as $item) {
            foreach ($item['statuses'] as $status) {
                $order['items'][] = array_merge($item, ['statusCode' => $status['code']]);
            }
        }

        return new DTO\Order($order);
    }
}
