<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class Promotional extends AbstractSender
{
    public function process(array $data) : void
    {
        if (!empty($data['users']) && is_array($data['users']) && !empty($data['text'])) {
            $this->sms($data);
        }
    }

    protected function sms(array $data) : void {
        foreach ($data['users'] as $curr) {
            if (preg_match("~^9[\d]{9}$~is", $curr['phone'])) {
                $queue = $this->getSmsTemplate();

                if (!empty($curr['type'])) {
                    $queue['sms_type'] = $curr['type'];
                }

                $queue['order_id'] = -1;
                $queue['phone'] = $curr['phone'];
                $queue['text'] = $data['text'];

                $this->appendQueue($queue);
            }
        }
    }
}