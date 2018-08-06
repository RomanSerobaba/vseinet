<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class AccountActivation extends AbstractSender
{
    public function process(array $data) : void
    {
        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->email($data);
        }
    }

    protected function email(array $data) : void {
        $queue = $this->getEmailTemplate();

        $body = [
            'content' => $this->getEmailBodyFromTemplate('ServiceBundle:Senders:AccountActivation.html.twig', [
                'id' => $data['id'],
            ]),
            'contentType' => parent::CONTENT_TYPE_HTML,
        ];

        $queue['body'] = $body;
        $queue['subject'] = 'Активация аккаунта';
        $queue['from']['addresses'] = parent::EMAIL_NOREPLY;
        $queue['addresses'] = $data['email'];

        $this->appendQueue($queue);
    }
}