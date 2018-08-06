<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class OrderShipped extends AbstractSender
{
    public function process(array $data) : void
    {
        if (!empty($data['phone'])) {
            $this->sms($data);
        }
    }

    protected function sms(array $data) : void {
        $queue = $this->getSmsTemplate();

        if (!empty($data['type'])) {
            $queue['sms_type'] = $data['type'];
        }

        $orderID = $data['order_id'];
        $date = !empty($data['date']) ? $data['date'] : '';
        $manager = $data['manager'];
        $deliveryType = $data['delivery_type'];

        if (!$date) {
            $date = date('d.m.Y', mktime(0, 0, 0, date('m'),   date('d')+10,   date('Y')));
        }
        if ($deliveryType == 'Самовывоз') {
            $text = 'Заказ '.$orderID.' будет '.$date.' после 15:00.Ждите смс о приходе товара';
        } else {
            $text = 'Заказ '.$orderID.' ожидается '.$date.'. Ждите звонка';
        }
        
        $queue['order_id'] = $data['order_id'];
        $queue['phone'] = $data['phone'];
        $queue['text'] = $text;
        $queue['viber'] = [
            'text' => 'Заказ №'.$orderID.' будет '.$date.' ориентировочно после 15:00. Ожидайте сообщения о приходе заказа. Возможна доставка '.parent::DELIVERY_PRICE.' т.'.$manager['phone'],
        ];

        $this->appendQueue($queue);
    }
}