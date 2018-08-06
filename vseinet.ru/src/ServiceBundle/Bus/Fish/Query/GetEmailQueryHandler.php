<?php

namespace ServiceBundle\Bus\Fish\Query;

use AppBundle\Bus\Message\MessageHandler;
use ServiceBundle\Services\AbstractSender;

class GetEmailQueryHandler extends MessageHandler
{
    /**
     * @param GetEmailQuery $query
     *
     * @return array
     */
    public function handle(GetEmailQuery $query) : array
    {
        $results = [];
        $sender = $this->get('service.sender');

        $data = [
            'email' => 'muromec@mail.ru',
            'id' => 12345,
        ];
        $results[] = $sender->send('account_activation', $data, true);

        $data = [
            'emails' => [
                'muromec@mail.ru' => [12345 => ['name' => 'Меч Кладенец',], 54321 => ['name' => 'Щит богатырский',],],
            ],
        ];
        $results[] = $sender->send('app_cancellation', $data, true);

        $data = [
            'order' => [
                'email' => 'muromec@mail.ru',
                'id' => 12345,
                'firstname' => 'Илья',
                'lastname' => 'Муромец',
                'order_date' => time(),
                'delivery_type' => 'Самовывоз',
                'phone' => '9023456677',
            ],
            'positions' => [
                ['name' => 'Меч Кладенец', 'quantity' => '1', 'status' => 'Статус',],
                ['name' => 'Щит богатырский', 'quantity' => '1', 'status' => 'Статус',],
            ],
        ];
        $results[] = $sender->send('can_be_payed', $data, true);

        $data = [
            'emails' => ['muromec@mail.ru',],
            'order_contract' => [
                'number' => 12345,
            ],
        ];
        $results[] = $sender->send('contract', $data, true);

        $data = [
            'email' => 'muromec@mail.ru',
            'order_delivery' => [
                'text' => 'Информация по доставке холодного оружия...',
                'order_id' => 12345,
            ],
        ];
        $results[] = $sender->send('delivery_info', $data, true);

        $data = [
            'emails' => ['muromec@mail.ru',],
            'order_invoice' => [
                'number' => 12345,
            ],
        ];
        $results[] = $sender->send('invoice', $data, true);

        $data = [
            'message_order' => [
                [
                    'without_notify' => 0,
                    'email' => 'muromec@mail.ru',
                    'firstname' => 'Илья',
                    'lastname' => 'Муромец',
                    'message_type' => 2,
                    'delivery_date' => time(),
                    'order_date' => time(),
                    'address' => 'Сторожка №1',
                    'id' => rand(3543, 123242),
                    'city' => 'Пенза',
                    'need_call' => 1,
                    'manager_phone' => AbstractSender::DEFAULT_MANAGER_PHONE,
                ],
            ],
            'positions' => [
                ['name' => 'Меч Кладенец', 'quantity' => '1', 'status' => 'Статус',],
                ['name' => 'Щит богатырский', 'quantity' => '1', 'status' => 'Статус',],
            ],
        ];
        $results[] = $sender->send('order_created', $data, true);

        $data = [
            'firstname' => 'Илья',
            'lastname' => 'Муромец',
            'email' => 'muromec@mail.ru',
            'password' => 'Gkdie29ka20lHa34g',
        ];
        $results[] = $sender->send('password_recovery', $data, true);

        $data = [
            'email' => 'muromec@mail.ru',
            'act' => ['id' => rand(3543, 123242),],
        ];
        $results[] = $sender->send('reconciliation_report', $data, true);

        $data = [
            'email' => 'muromec@mail.ru',
            'order_id' => rand(3543, 123242),
            'user_name' => 'Илья Муромец',
            'new_trading_code' => 'KLW307',
            'title_trading_code' => 'FGW7K2',
            'transport_company' => 'ТК ДЛ',
            'title_transport_company' => 'Деловые линии',
        ];
        $results[] = $sender->send('shipment_code', $data, true);

        $data = [
            'email' => 'muromec@mail.ru',
            'order_id' => rand(3543, 123242),
            'user_name' => 'Илья Муромец',
            'new_trading_code' => 'KLW307',
            'title_trading_code' => 'FGW7K2',
            'transport_company' => 'ТК ДЛ',
            'title_transport_company' => 'Деловые линии',
        ];
        $results[] = $sender->send('shipment_code_edited', $data, true);

        $data = [
            'email' => 'muromec@mail.ru',
        ];
        $results[] = $sender->send('supplier_reclamation', $data, true);

        return $results;
    }
}