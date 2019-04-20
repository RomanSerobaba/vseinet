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
            return null;
        }

        $api = $this->get('user.api.client');
        try {
            $parameters = [
                'financialCounteragentId' => $counteragent->getId(),
                'page' => $query->page,
                'limit' => $query->limit,
            ];
            $result = $api->get('/api/v1/orders/?'.http_build_query($parameters));
        } catch (BadRequestHttpException $e) {
            return null;
        }

        if (0 === $result['total']) {
            return null;
        }

        $history = [
            'total' => $result['total'],
            'orders' => [],
        ];

        foreach ($result['orders'] as $order) {
            foreach ($result['orderItems'] as $item) {
                foreach ($item['statuses'] as $status) {
                    $order['items'][] = array_merge($item, ['statusCode' => $status['code']]);
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

            $history['orders'][] = new DTO\Order($order);
        }

        return new DTO\History($history);
    }
}
