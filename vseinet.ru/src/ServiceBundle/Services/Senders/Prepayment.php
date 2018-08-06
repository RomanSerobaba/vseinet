<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class Prepayment extends AbstractSender
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

        $queue['order_id'] = $data['order_id'];
        $queue['phone'] = $data['phone'];
        $queue['text'] = 'Заказ '.$data['order_id'].' требует предоплаты до '.$data['date'].' для получения его '.$data['delivery_date'];

        $this->appendQueue($queue);
    }
}