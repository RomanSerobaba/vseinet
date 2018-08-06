<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class OrderReminder extends AbstractSender
{
    public function process(array $data) : void
    {
        if (!empty($data['positions']) && is_array($data['positions'])) {
            $this->sms($data['positions']);
        }
    }

    protected function sms(array $data) : void {
        foreach ($data['positions'] as $order) {
            if ($order['phone']) {
                $queue = $this->getSmsTemplate();

                if (!empty($data['type'])) {
                    $queue['sms_type'] = $data['type'];
                }

                $isFull = true;
                $orderID = $order['id'];
//                $address = $order['short_address'] ? $order['short_address'] : 'Ул.Суворова 225';
//                $manager = array('phone' => $order['short_phone'] ?: '290708', 'firstname' => $order['firstname'],);

                if ($isFull && !empty($order['is_old'])) {
                    $text = 'Убедительная просьба забрать заказ '.$orderID;
                } else {
                    if ($isFull) {
                        $text = 'Просьба забрать заказ '.$orderID.' иначе он будет аннулирован';
                    } else {
                        $text = 'Ваш заказ '.$orderID.' будет аннулирован через 3 дня';
                    }
                }

                $queue['order_id'] = $orderID;
                $queue['phone'] = $order['phone'];
                $queue['text'] = $text;

                $this->appendQueue($queue);
            }
        }
    }
}