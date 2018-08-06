<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class DeliveryInfo extends AbstractSender
{
    public function process(array $data) : void
    {
        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->email($data);
        }
    }

    protected function email(array $data) : void {
        $queue = $this->getEmailTemplate();

        $body = [
            'content' => $this->getEmailBodyFromTemplate('ServiceBundle:Senders:DeliveryInfo.html.twig', [
                'order_delivery' => $data['order_delivery'],
            ]),
            'contentType' => parent::CONTENT_TYPE_HTML,
        ];

        $queue['body'] = $body;
        $queue['subject'] = 'Доставка заказа '.$data['order_delivery']['order_id'];
        $queue['from']['addresses'] = !empty($data['from_email']) ? $data['from_email'] : parent::EMAIL_MAIL;
        $queue['addresses'] = $data['email'];

        $this->appendQueue($queue);
    }
}