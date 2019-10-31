<?php

namespace AppBundle\Bus\Order\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Entity\FinancialCounteragent;

class GetHistoryQueryHandler extends MessageHandler
{
    public function handle(GetHistoryQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        $counteragent = $em->getRepository(FinancialCounteragent::class)->findOneBy(['userId' => $this->getUser()->getId()]);
        if (!$counteragent instanceof FinancialCounteragent) {
            return new DTO\History(['items' => [], 'total' => 0]);
        }

        $api = $this->get('site.api.client');
        try {
            $parameters = [
                'financialCounteragentId' => $counteragent->getId(),
                'page' => $query->page,
                'limit' => $query->limit,
            ];
            $result = $api->get('/api/v1/orders/?'.http_build_query($parameters));
        } catch (BadRequestHttpException $e) {
            return new DTO\History(['items' => [], 'total' => 0]);
        }

        if (0 === $result['total']) {
            return new DTO\History(['items' => [], 'total' => 0]);
        }

        $history = ['items' => [], 'total' => $result['total']];

        foreach ($result['orders'] as $order) {
            foreach ($result['orderItems'] as $item) {
                // $item['productAvailability'] = $this->get('query_bus')->handle(new GetProductAvailability(['baseProductId' => $item['productAvailability']]));
                if ($order['id'] == $item['orderId']) {
                    foreach ($item['statuses'] as $status) {
                        $order['items'][] = array_merge($item, [
                            'statusCode' => $status['code'],
                            'statusCodeName' => $status['clientName'],
                            'deliveryDate' => $status['deliveryDate'] ?? null,
                        ]);
                    }
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

            $history['items'][] = $order;
        }

        return new DTO\History($history);
    }
}
