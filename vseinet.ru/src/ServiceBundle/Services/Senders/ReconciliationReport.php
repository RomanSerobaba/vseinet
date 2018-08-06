<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class ReconciliationReport extends AbstractSender
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
            'content' => $this->getEmailBodyFromTemplate('ServiceBundle:Senders:ReconciliationReport.html.twig', [
                'act' => $data['act'],
            ]),
            'contentType' => parent::CONTENT_TYPE_HTML,
        ];

        $queue['body'] = $body;
        $queue['subject'] = 'Акт сверки '.$data['act']['id'];
        if (!empty($data['file'])) {
            $queue['file'] = $data['file'];
        }
        $queue['from']['addresses'] = parent::EMAIL_BUH;
        $queue['addresses'] = $data['email'];

        $this->appendQueue($queue);
    }
}