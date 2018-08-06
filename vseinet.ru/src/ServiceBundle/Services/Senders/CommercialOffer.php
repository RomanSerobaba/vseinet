<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class CommercialOffer extends AbstractSender
{
    public function process(array $data) : void
    {
        if (!empty($data['emails']) && is_array($data['emails'])) {
            $this->email($data);
        }
    }

    protected function email(array $data) : void {
        $queue = $this->getEmailTemplate();

        $body = [
            'content' => $data['body'],
            'contentType' => parent::CONTENT_TYPE_HTML,
        ];

        $queue['body'] = $body;
        $queue['subject'] = $data['subject'];
        if (!empty($data['file'])) {
            $queue['file'] = $data['file'];
        }
        $queue['from']['addresses'] = parent::EMAIL_NOREPLY;

        foreach ($data['emails'] as $email => $emailData) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $queue['addresses'][$email] = $emailData; // [id, name]
            }
        }

        if (!empty($queue['addresses'])) {
            $this->appendQueue($queue);
        }
    }
}