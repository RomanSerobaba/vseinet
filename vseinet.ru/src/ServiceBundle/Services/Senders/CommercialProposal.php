<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class CommercialProposal extends AbstractSender
{
    public function process(array $data) : void
    {
        $emails = $data['emails'];

        if (!empty($emails) && is_array($emails)) {
            $this->email($data);
        }
    }

    protected function email(array $data) : void {
        $orderKp = $data['order_kp'];
        $file = $data['file'];

        foreach ($data['emails'] as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $queue = $this->getEmailTemplate();

                $body = [
                    'content' => $this->getEmailBodyFromTemplate('ServiceBundle:Senders:CommercialProposal.html.twig', [
                        'order_kp' => $orderKp,
                    ]),
                    'contentType' => parent::CONTENT_TYPE_HTML,
                ];

                $queue['body'] = $body;
                $queue['file'] = $file;
                $queue['subject'] = 'Коммерческое предложение '.$orderKp['number'];
                $queue['from']['addresses'] = parent::EMAIL_BUH;
                $queue['addresses'] = $email;

                $this->appendQueue($queue);
            }
        }
    }
}