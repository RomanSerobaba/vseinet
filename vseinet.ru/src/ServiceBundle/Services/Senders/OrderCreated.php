<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class OrderCreated extends AbstractSender
{
    public function process(array $data) : void
    {
        $order = $data['order'];
        $messageOrder = !empty($data['message_order']) ? $data['message_order'] : [];

        if (!empty($messageOrder)) {
            $messageOrder = $messageOrder[0];

            if (!$messageOrder['without_notify']) {
                if (!empty($messageOrder['email']) && filter_var($messageOrder['email'], FILTER_VALIDATE_EMAIL)) {
                    $this->email($data);
                }
                if ($order['phone']) {
                    $this->sms($data);
                }
            }
        }
    }

    protected function sms(array $data) : void {
        $queue = $this->getSmsTemplate();

        $manager = $data['manager'];
        $order = $data['order'];

        if ($manager['manager_id'] == parent::DEFAULT_MANAGER_ID) {
            $manager['phone'] = parent::DEFAULT_MANAGER_PHONE;
        }
        if (!$manager['phone']) {
            $manager['phone'] = $data['default_manager_phone'];
        }

        if(empty($order['need_call'])) {
            $text = 'Ваш заказ '.$order['id'].'. Контактный телефон: '.$manager['phone'].'.';
        } else {
            if (date('w')==5 && date('H:i')>'18:00' || date('w')==6 || date('w')==7) {
                $text = 'Заказ '.$order['id'].'. Свяжутся '.date('d.m',mktime(10, 10, 10, date("m"), date("d")+(8-date('w')), date("Y"))).' для подтверждения.Телефон: '.$manager['phone'].'.';
            } else {
                $text = 'Заказ '.$order['id'].'. Ждите подтверждения. Контактный телефон: '.$manager['phone'].'.';
            }
        }

        if (!empty($data['type'])) {
            $queue['sms_type'] = $data['type'];
        }

        $queue['order_id'] = $order['id'];
        $queue['phone'] = $order['phone'];
        $queue['text'] = $text;

        $this->appendQueue($queue);
    }

    protected function email(array $data) : void {
        $queue = $this->getEmailTemplate();

        $messageOrder = !empty($data['message_order']) ? $data['message_order'] : [];
        $positions = $data['positions'];

        $body = [
            'content' => $this->getEmailBodyFromTemplate('ServiceBundle:Senders:OrderCreated.html.twig', [
                'positions' => $positions,
                'order' => $messageOrder,
            ]),
            'contentType' => parent::CONTENT_TYPE_HTML,
        ];

        $queue['body'] = $body;
        $queue['subject'] = 'Ваш заказ #'.$messageOrder['id'].' был принят на обработку';
        $queue['from']['addresses'] = parent::EMAIL_NOREPLY;
        $queue['addresses'] = $messageOrder['email'];

        $this->appendQueue($queue);
    }
}