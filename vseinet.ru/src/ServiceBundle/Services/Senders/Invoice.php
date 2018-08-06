<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class Invoice extends AbstractSender
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
                    'content' => $this->getEmailBodyFromTemplate('ServiceBundle:Senders:Invoice.html.twig', [
                        'order_invoice' => $data['order_invoice'],
                    ]),
                    'contentType' => parent::CONTENT_TYPE_HTML,
                ];

                $queue['body'] = $body;
                if (!empty($data['file'])) {
                    $queue['file'] = $data['file'];
                }
                $queue['subject'] = 'Счет '.$data['order_invoice']['number'];
                $queue['from']['addresses'] = parent::EMAIL_BUH;
                $queue['addresses'] = $email;

                $this->appendQueue($queue);
            }
        }
    }
}