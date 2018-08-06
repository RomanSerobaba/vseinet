<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class AppCancellation extends AbstractSender
{
    public function process(array $data): void
    {
        $emails = $data['emails'];

        if (!empty($emails) && is_array($emails)) {
            $this->email($data);
        }
    }

    protected function email(array $data): void
    {
        foreach ($data['emails'] as $email => $positions) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $queue = $this->getEmailTemplate();

                $body = [
                    'content' => $this->getEmailBodyFromTemplate('ServiceBundle:Senders:AppCancellation.html.twig', [
                        'positions' => $positions,
                    ]),
                    'contentType' => parent::CONTENT_TYPE_HTML,
                ];

                $queue['body'] = $body;
                $queue['subject'] = 'Товар по ' . (count($positions) > 1 ? 'Вашим заявкам #' : 'Вашей заявке #') . implode(',',
                        array_keys($positions)) . ' не поступил на склад';
                $queue['from']['addresses'] = parent::EMAIL_NOREPLY;
                $queue['addresses'] = $email;

                $this->appendQueue($queue);
            }
        }
    }
}