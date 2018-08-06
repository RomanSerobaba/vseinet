<?php

namespace ServiceBundle\Bus\Fish\Query;

use AppBundle\Bus\Message\MessageHandler;
use ServiceBundle\Services\AbstractSender;

class GetSmsQueryHandler extends MessageHandler
{
    /**
     * @param GetSmsQuery $query
     *
     * @return array
     */
    public function handle(GetSmsQuery $query) : array
    {
        $results = [];
        $sender = $this->get('service.sender');

        $data = [
            'phone' => '9023456677',
            'type' => rand(1, 13),
            'order_id' => rand(456, 573422),
            'representative_id' => $this->getParameter('default.point.id'),
            'date' => date('d.m.Y'),
            'delivery_date' => date('d.m.Y'),
            'is_full' => 1,
            'delivery_type' => 'Самовывоз',
            'manager' => [
                'name' => 'Леший',
            ],
            'schedule' => 'с 9.00 до 18.00',
            'order' => ['need_call' => 0, 'id' => rand(456, 573422), 'phone' => '9023456677',],
            'message_order' => [['without_notify' => 0,],],
            'orders' => [
                [
                    'phone' => '9023456677',
                    'without_notify' => 1,
                    'address_id' => 1,
                    'is_full' => 1,
                    'id' => rand(456, 573422),
                    'courier_phone' => AbstractSender::DEFAULT_MANAGER_PHONE,
                ],
            ],
            'positions' => [
                ['phone' => '9023456677', 'id' => rand(456, 573422), 'is_old' => 1,],
            ],
            'users' => [
                ['phone' => '9023456677', 'type' => rand(1, 13),],
            ],
            'text' => 'Какой то текст',
            'title_trading_code' => 'FGW7K2',
            'new_trading_code' => 'KLW307',
            'user_name' => 'Илья Муромец',
        ];
        $results[] = $sender->send('can_be_payed', $data, true);
        $results[] = $sender->send('not_reached', $data, true);
        $results[] = $sender->send('order_arrived', $data, true);
        $results[] = $sender->send('order_created', $data, true);
        $results[] = $sender->send('order_on_delivery', $data, true);
        $results[] = $sender->send('order_reminder', $data, true);
        $results[] = $sender->send('order_shipped', $data, true);
        $results[] = $sender->send('password_recovery', $data, true);
        $results[] = $sender->send('prepayment', $data, true);
        $results[] = $sender->send('promotional', $data, true);
        $results[] = $sender->send('shipment_code', $data, true);
        $results[] = $sender->send('shipment_code_edited', $data, true);

        foreach ($results as $i => $result) {
            $results[$i]['text_size'] = $this->_calcTextSize($result['text']);
        }

        return $results;
    }

    /**
     * @param string $text
     *
     * @return int
     */
    private function _calcTextSize(string $text) : int
    {
        return mb_strlen($text);
    }
}