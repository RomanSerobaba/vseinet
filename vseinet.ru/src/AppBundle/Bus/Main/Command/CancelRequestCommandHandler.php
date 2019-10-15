<?php

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\OrderDoc;

class CancelRequestCommandHandler extends MessageHandler
{
    public function handle(CancelRequestCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository(OrderDoc::class)->find($command->id);
        $order->setIsCancelRequested(true);

        $api = $this->get('site.api.client');

        $params = [
            'text' => 'Клиент хочет отказаться от заказа. Причина отказа: '.$command->comment,
            'type' => 'client',
            'isImportant' => true,
        ];

        try {
            $result = $api->post('/api/v1/orders/'.$order->getDId().'/comments/', [], $params);
            $command->id = $result['id'];
        } catch (BadRequestHttpException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
