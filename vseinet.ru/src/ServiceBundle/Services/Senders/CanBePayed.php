<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class CanBePayed extends AbstractSender
{
    public function process(array $data) : void
    {
        if (!empty($data['phone'])) {
            $this->sms($data);
        }
        if (!empty($data['order'])) {
            $this->email($data);
        }
    }

    protected function sms(array $data) : void {
        $queue = $this->getSmsTemplate();

        if (!empty($data['type'])) {
            $queue['sms_type'] = $data['type'];
        }

        $orderID = $data['order_id'];

        $queue['order_id'] = $orderID;
        $queue['phone'] = $data['phone'];
        $queue['text'] = 'Заказ №'. $orderID . ' обработан. Можно оплатить https://vseinet.ru/order/status/?number=' . $orderID;

        $this->appendQueue($queue);
    }

    protected function email(array $data) : void {
        $messageOrder = !empty($data['order']) ? $data['order'] : [];
        $positions = !empty($data['positions']) ? $data['positions'] : [];

        if (!empty($messageOrder['email']) && filter_var($messageOrder['email'], FILTER_VALIDATE_EMAIL)) {
            $queue = $this->getEmailTemplate();

            $body = [
                'content' => $this->getEmailBodyFromTemplate('ServiceBundle:Senders:CanBePayed.html.twig', [
                    'positions' => $positions,
                    'order' => $messageOrder,
                ]),
                'contentType' => parent::CONTENT_TYPE_HTML,
            ];

            $queue['body'] = $body;
            $queue['subject'] = 'Ваш заказ #'.$messageOrder['id'].' можно оплачивать';
            $queue['from']['addresses'] = parent::EMAIL_NOREPLY;
            $queue['addresses'] = $messageOrder['email'];

            $this->appendQueue($queue);
        }
    }
}