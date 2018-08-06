<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class OrderOnDelivery extends AbstractSender
{
    public function process(array $data) : void
    {
        if (!empty($data['orders']) && is_array($data['orders'])) {
            $this->sms($data);
        }
    }

    protected function sms(array $data) : void {
        foreach ($data['orders'] as $order) {
            if ($order['phone'] && !$order['without_notify'] && !$order['address_id']) {
                $queue = $this->getSmsTemplate();

                if (!empty($data['type'])) {
                    $queue['sms_type'] = $data['type'];
                }

                $orderID = $order['id'];
                $manager = ['phone' => $order['courier_phone'],];

                if ($order['is_full']) {
                    $text = 'Заказ '.$orderID.' передан на доставку. Тел. курьера '.($manager['phone']?$manager['phone']:parent::DELIVERY_SERVICE_PHONE);
                } else {
                    $text = 'Часть заказа '.$orderID.' передана на доставку. Тел. курьера '.($manager['phone']?$manager['phone']:parent::DELIVERY_SERVICE_PHONE);
                }

                $queue['order_id'] = $orderID;
                $queue['phone'] = $order['phone'];
                $queue['text'] = $text;

                $this->appendQueue($queue);
            }
        }
    }
}