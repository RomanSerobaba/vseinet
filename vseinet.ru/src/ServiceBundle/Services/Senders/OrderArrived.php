<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class OrderArrived extends AbstractSender
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
        $representativeID = $data['representative_id'];
        $schedule = $data['schedule'];
        $manager = $data['manager'];
        $isFull = !empty($data['is_full']);
        $deliveryType = !empty($data['delivery_type']) ? $data['delivery_type'] : 'Самовывоз';

        if (empty($manager['phone'])) {
            $manager['phone'] = parent::DEFAULT_MANAGER_PHONE;
        }

        $pointInfo = $this->getPointInfo($representativeID, $orderID, $isFull);
        $viber = [
            'text' => '',
            'imageURL' => $pointInfo['img'],
            'buttonText' => 'Подробнее',
            'buttonURL' => 'https://vseinet.ru/order/status/?number='.$orderID,
            'isPromotional' => false,
        ];

        if ($isFull) {
            if ($deliveryType === 'Самовывоз') {
                $text = 'Заказ '.$orderID.' прибыл.'.$schedule.'. Возможна доставка '.parent::DELIVERY_PRICE.' т.'.$manager['phone'];

                $viber['text'] = 'Заказ '.$orderID.' прибыл.'.PHP_EOL;
                $viber['text'] .= 'г. '.$pointInfo['city'].', '.$pointInfo['point'].', '.$pointInfo['address'].PHP_EOL;
                $viber['text'] .= $schedule;
            } else {
                $text = 'Заказ '.$orderID.' прибыл. Ожидайте доставку т.'.parent::DELIVERY_SERVICE_PHONE;

                $viber['text'] = $text;
            }
        } else {
            $viber['text'] = 'Часть заказа '.$orderID.' прибыла.'.PHP_EOL;

            if (!empty($pointInfo['positions']) && count($pointInfo['positions']) <= 3) {
                foreach ($pointInfo['positions'] as $position) {
                    $viber['text'] .= $position['name'].', '.$position['quantity'] . ' шт.'.PHP_EOL;
                }
            }

            $viber['text'] .= 'Ожидайте смс о прибытии оставшейся части заказа.'.PHP_EOL;
            $viber['text'] .= 'г. '.$pointInfo['city'].', '.$pointInfo['point'].', '.$pointInfo['address'].PHP_EOL;
            $viber['text'] .= $schedule;

            if ($deliveryType === 'Самовывоз') {
                $text = 'Часть заказа '.$orderID.' прибыла.'.$schedule.'.Оформ.доставку '.parent::DELIVERY_PRICE.' т.'.$manager['phone'];
            } elseif ($deliveryType === 'Курьерская') {
                $text = 'Часть заказа '.$orderID.' прибыла.'.$schedule.'. Статус заказа на сайте vseinet.ru';
            } else {
                $text = 'Часть заказа '.$orderID.' прибыла. Тел. службы доставки '.parent::DELIVERY_SERVICE_PHONE;
            }
        }

        $queue['order_id'] = $orderID;
        $queue['phone'] = $data['phone'];
        $queue['text'] = $text;
        $queue['viber'] = $viber;

        $this->appendQueue($queue);
    }
}