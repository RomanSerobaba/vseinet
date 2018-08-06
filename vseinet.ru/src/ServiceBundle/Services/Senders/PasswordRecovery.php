<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class PasswordRecovery extends AbstractSender
{
    public function process(array $data) : void
    {
        if (!empty($data['phone'])) {
            $this->sms($data);
        }
        if (!empty($data['user'])) {
            $this->email($data['user']);
        }
    }

    protected function sms(array $data) : void {
        $queue = $this->getSmsTemplate();

        if (!empty($data['type'])) {
            $queue['sms_type'] = $data['type'];
        }

        $queue['order_id'] = 0;
        $queue['phone'] = $data['phone'];
        $queue['text'] = 'Ваш пароль на сайте: '.$data['password'];

        $this->appendQueue($queue);
    }

    protected function email(array $data) : void {
        if (!empty($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $queue = $this->getEmailTemplate();

            $body = [
                'content' => $this->getEmailBodyFromTemplate('ServiceBundle:Senders:PasswordRecovery.html.twig', [
                    'user' => $data,
                ]),
                'contentType' => parent::CONTENT_TYPE_HTML,
            ];

            $queue['body'] = $body;
            $queue['subject'] = 'Напоминание пароля учетной записи';
            $queue['from']['addresses'] = parent::EMAIL_NOREPLY;
            $queue['addresses'] = $data['email'];

            $this->appendQueue($queue);
        }
    }
}