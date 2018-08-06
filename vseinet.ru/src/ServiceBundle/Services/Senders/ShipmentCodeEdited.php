<?php

namespace ServiceBundle\Services\Senders;

use ServiceBundle\Services\AbstractSender;

class ShipmentCodeEdited extends AbstractSender
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

        $titleTradingCodeSmall = mb_strtolower($data['title_trading_code'], 'UTF-8');

        $queue['order_id'] = $data['order_id'];
        $queue['phone'] = $data['phone'];
        $queue['text'] = 'Правильный '.$titleTradingCodeSmall.' по заказу '.$data['order_id'].': '.$data['new_trading_code'];

        $this->appendQueue($queue);
    }

    protected function email(array $data) : void {
        $queue = $this->getEmailTemplate();

        $body = [
            'content' => $this->getEmailBodyFromTemplate('ServiceBundle:Senders:ShipmentCodeEdited.html.twig', [
                'orderId' => $data['order_id'],
                'userName' => $data['user_name'],
                'newTradingCode' => $data['new_trading_code'],
                'titleTradingCode' => $data['title_trading_code'],
                'titleTradingCodeSmall' => mb_strtolower($data['title_trading_code'], 'UTF-8'),
                'oldTradingCode' => 'Старый '.mb_strtolower($data['title_trading_code'], 'UTF-8').' более не действителен.',
                'transportCompany' => $data['transport_company'],
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