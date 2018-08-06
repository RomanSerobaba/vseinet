<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class SupplierReclamation extends AbstractSender
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
            'content' => $this->getEmailBodyFromTemplate('ServiceBundle:Senders:SupplierReclamation.html.twig', []),
            'contentType' => parent::CONTENT_TYPE_HTML,
        ];

        if (!empty($data['file'])) {
            $queue['file'] = $data['file'];
        }
        $queue['body'] = $body;
        $queue['subject'] = 'Возврат товара '.date('d-m-Y');
        $queue['from']['addresses'] = parent::EMAIL_NOREPLY;
        $queue['addresses'] = $data['email'];

        $this->appendQueue($queue);
    }
}