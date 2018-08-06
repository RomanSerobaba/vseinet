<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class NotReached extends AbstractSender
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
        $queue['text'] = 'Менеджер не дозвонился по заказу '.$queue['order_id'].'.Позвоните по т.'.$data['manager']['phone'].'. '.$data['manager']['firstname'];

        $this->appendQueue($queue);
    }
}