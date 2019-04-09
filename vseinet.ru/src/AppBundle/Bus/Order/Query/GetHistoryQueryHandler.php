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
            $history = $api->get('/api/v1/orders/?'.http_build_query($parameters));
        } catch (BadRequestHttpException $e) {
            return null;
        }

        return new DTO\History($history);
    }
}
