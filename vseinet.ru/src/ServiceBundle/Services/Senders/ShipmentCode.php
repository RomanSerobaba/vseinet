<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class ShipmentCode extends AbstractSender
{
    public function process(array $data) : void
    {
        if (!empty($data['phone'])) {
            $this->sms($data);
        }
        if (!empty($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->email($data);
        }
    }

    protected function sms(array $data) : void {
        $queue = $this->getSmsTemplate();

        if (!empty($data['type'])) {
            $queue['sms_type'] = $data['type'];
        }

        $queue['order_id'] = $data['order_id'];
        $queue['phone'] = $data['phone'];
        $queue['text'] = 'Заказ '.$data['order_id'].' отправлен. '.$data['title_trading_code'].' '.$data['new_trading_code'].', '.$data['user_name'];

        $this->appendQueue($queue);
    }

    protected function email(array $data) : void {
        $queue = $this->getEmailTemplate();

        $body = [
            'content' => $this->getEmailBodyFromTemplate('ServiceBundle:Senders:ShipmentCode.html.twig', [
                'orderId' => $data['order_id'],
                'userName' => $data['user_name'],
                'newTradingCode' => $data['new_trading_code'],
                'titleTradingCode' => $data['title_trading_code'],
                'titleTradingCodeSmall' => mb_strtolower($data['title_trading_code'], 'UTF-8'),
                'oldTradingCode' => '',
                'transportCompany' => $data['transport_company'],
                'titleTransportCompany' => $data['title_transport_company'],
                'new' => '1',
            ]),
            'contentType' => parent::CONTENT_TYPE_HTML,
        ];

        $queue['body'] = $body;
        $queue['subject'] = 'Информация по доставке заказа '.$data['order_id'];
        $queue['from']['addresses'] = parent::EMAIL_NOREPLY;
        $queue['addresses'] = $data['email'];

        $this->appendQueue($queue);
    }
}