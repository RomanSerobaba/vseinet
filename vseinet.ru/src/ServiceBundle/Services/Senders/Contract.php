<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class Contract extends AbstractSender
{
    public function process(array $data) : void
    {
        $emails = $data['emails'];

        if (!empty($emails) && is_array($emails)) {
            $this->email($data);
        }
    }

    protected function email(array $data) : void {
        foreach ($data['emails'] as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $queue = $this->getEmailTemplate();

                $body = [
                    'content' => $this->getEmailBodyFromTemplate('ServiceBundle:Senders:Contract.html.twig', [
                        'order_contract' => $data['order_contract'],
                    ]),
                    'contentType' => parent::CONTENT_TYPE_HTML,
                ];

                $queue['body'] = $body;
                if (!empty($data['file'])) {
                    $queue['file'] = $data['file'];
                }
                $queue['subject'] = 'Договор поставки '.$data['order_contract']['number'];
                $queue['from']['addresses'] = parent::EMAIL_BUH;
                $queue['addresses'] = $email;

                $this->appendQueue($queue);
            }
        }
    }
}