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
            $result = $api->get('/api/v1/orders/'.$query->id.'/');
        } catch (BadRequestHttpException $e) {
            return null;
        }

        if (empty($result['order'])) {
            return null;
        }

        $order = $result['order'];

        foreach ($result['orderItems'] as $item) {
            foreach ($item['statuses'] as $status) {
                $order['items'][] = array_merge($item, [
                    'quantity' => $status['quantity'],
                    'statusCode' => $status['code'],
                    'statusCodeName' => $status['clientName'],
                    'deliveryDate' => $status['deliveryDate'] ?? null,
                ]);
            }
        }

        if (!empty($result['persons'])) {
            foreach ($result['persons'] as $person) {
                if ($person['id'] == $order['personId']) {
                    $order['personName'] = $person['fullname'];
                    $order['contacts'] = $person['contacts'];
                }
            }
        }

        return new DTO\Order($order);
    }
}
