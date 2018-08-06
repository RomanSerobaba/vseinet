<?php

namespace ServiceBundle\Components\sms;

use ServiceBundle\Components\AbstractSms;
use ServiceBundle\Entity\NotificationLog;
use ServiceBundle\Services\AbstractSender;

class RedSms extends AbstractSms
{
    const DESPONSE_ALREADY = 'СМС была ранее отправлена.';

    protected $path = 'https://adm.redsms.ru/xml/';
    protected $login = 'Vseinet';
    protected $password = 'o5uZLnU1yowDnlc8C9GZ';
    protected $serviceName = NotificationLog::SERVICE_NAME_REDSMS;
    protected $statusMask = [
        'error' => NotificationLog::STATUS_FAILED,
        'not_deliver' => NotificationLog::STATUS_FAILED,
        'expired' => NotificationLog::STATUS_FAILED,
        'send' => NotificationLog::STATUS_PENDING,
        'deliver' => NotificationLog::STATUS_DELIVERED,
        'partly_deliver' => NotificationLog::STATUS_PENDING,
    ];

    protected $errorMask = [
        1 => 'The subscriber is absent or out of a coverage',//Абонент недоступен или отключен
        2 => 'Call barred service activated',//У абонента включен запрет на прием сообщений или абонента заблокировал оператор (возможно, в связи с отрицательным балансом)
        3 => 'Unknown subscriber',//Номер телефона не существует или не обслуживается
        4 => 'Memory capacity exceeded',//Память телефона абоненета переполнена
        5 => 'Equipment protocol error',//Аппаратная ошибка телефона абонента
        6 => 'Teleservice not provisioned',//Сервис коротких сообщений не предоставляется
        7 => 'Facility not supported',//Аппарат абонента не поддерживает прием коротких сообщений
        8 => 'Subscriber is busy',//Аппарат абонента занят операцией, препятствующей получению короткого сообщения
        9 => 'Roaming restrictions',//Абонент находится в роуминге
        10 => 'Timeout',//Время ожидания ответа от SMSC абонента истекло
        11 => 'SS7 routing error',//Внутренняя ошибка маршрутизации
        12 => 'Internal system failure',//Внутренняя ошибка системы
        13 => 'SMSC failure'//Ошибка коммутатора (внутренняя ошибка передачи данных)
    ];

    public function send(): bool
    {
        $isSuccess = false;
        $statuses = $message = [];

        if ($this->isActive) {
            $temp_log_array = [];
            foreach ($this->getData() as $key => $curr) {
                $curr['type'] = $this->getSenderType();
                $this->debug('Data for RedSMS:' . print_r($curr, true));
                $this->debug('sms_id: ' . 'err_'.time()."_".(isset($curr['order_id'])?$curr['order_id']:'')."_".$curr['type']);

                if ($curr['phone'] != parent::DEFECTIVE_PHONE && substr($curr['phone'], 0, 1) == '9') {
                    $message[] = '<message><sender>' . $this->sender . '</sender><text>' . $curr['text'] . '</text><abonent phone="7' . $curr['phone'] . '" number_sms="'.$key.'" client_id_sms="'.(isset($curr['order_id'])?$curr['order_id']:'').time().'"/></message>';

                    $temp_log_array[] = [
                        'temp_id' => $this->generateTempID($curr),
                        'status' => NotificationLog::STATUS_FAILED,
                        'type' => $curr['type'],
                        'text' => (string)$curr['text'],
                        'addressee' => (string)$curr['phone'],
                        'service_name' => $this->serviceName,
                        'order_id' => (isset($curr['order_id'])?$curr['order_id']:0),
                        'created_at' => date('Y-m-d H:i:s'),
                        'channel' => NotificationLog::CHANNEL_SMS,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'amount' => 0,
                    ];
                } else {
                    $this->debug('Defective number: ' . $curr['phone'].'. Skipped...');
                }
            }

            if (empty($message)) {
                $this->debug('Empty message...');

                return $isSuccess;
            }

            $this->debug('Logs:');
            print_r($temp_log_array);

            $temp_ids = array();
            try {
                $temp_ids = $this->saveLogs($temp_log_array);
            } catch (\Exception $e) {
                $this->debug('Save log error: '.$e->getMessage());
            }

            $this->debug('Saved logs:');
            print_r($temp_ids);

            $xml = '<?xml version="1.0" encoding="utf-8"?><request><security><login value="' . $this->login . '"/><password value="' . $this->password . '"/></security>' . implode('', $message) . '</request>';

            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $xml);
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, array("Content-type: text/xml; charset=utf-8"));

            $data = curl_exec($this->ch);

            if (strlen($data ?: '')) {
                $xml = new \SimpleXMLElement($data);
                $messages = $xml->children();

                $i = 0;
                /**
                 * @var \SimpleXMLElement $message
                 */
                foreach ($messages as $message) {
                    if ($message->getName() === 'information') {
                        if (((String) $message === 'send' && (String) $message['id_sms']) || (String) $message === self::DESPONSE_ALREADY) {
                            $statuses[$i] = array(
                                'id' => $temp_ids[$i],
                                'native_status' => 'send',
                                'native_id' => (String) $message['id_sms'],
                                'status' => NotificationLog::STATUS_DELIVERED,
                                'amount' => (int) $message['parts'],
                                'type' => $this->getData()[$i]['type'],
                                'updated_at' => date('Y-m-d H:i:s'),
                                'temp_id' => null,
                            );

                            $isSuccess = true;
                        } else {
                            $statuses[$i] = array(
                                'id' => $temp_ids[$i],
                                'native_status' => 'error / '.(String) $message,
                                'status' => NotificationLog::STATUS_FAILED,
                                'amount' => 0,
                                'type' => $this->getData()[$i]['type'],
                                'updated_at' => date('Y-m-d H:i:s'),
                                'temp_id' => null,
                            );
                        }
                    } elseif ($message->getName() === 'error') {
                        $statuses[$i] = array(
                            'id' => $temp_ids[$i],
                            'native_status' => 'error / '.(String) $message,
                            'status' => NotificationLog::STATUS_FAILED,
                            'amount' => 0,
                            'type' => $this->getData()[$i]['type'],
                            'updated_at' => date('Y-m-d H:i:s'),
                            'temp_id' => null,
                        );

                        $this->debug('Error: '.(String) $message);
                    }

                    $i++;
                }
            }

            if (count($statuses)) {
                try {
                    $this->saveLogs($statuses);
                } catch (\Exception $e) {
                    $this->debug('Save log error: '.$e->getMessage());
                }
            }

            parent::send();
        }

        return $isSuccess;
    }

    public function checkStatus($msgs): void
    {
        if ($this->isActive) {
            $message = [];
            foreach ($msgs as $smsId => $currStatus) {
                $message[] = '<id_sms>' . $smsId . '</id_sms>';
            }

            $xml = '<?xml version="1.0" encoding="utf-8" ?><request><security><login value="' . $this->login . '"/><password value="' . $this->password . '"/></security><get_state>' . implode('', $message) . '</get_state></request>';
            curl_setopt($this->ch, CURLOPT_URL, 'https://adm.redsms.ru/xml/state.php');
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $xml);
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, array("Content-type: text/xml; charset=utf-8"));
            $data = curl_exec($this->ch);

            $xml = new \SimpleXMLElement($data);
            $messages = $xml->children();
            $statuses = [];

            foreach ($messages as $message) {
                if ($message->getName() === 'state' && (String) $message !== 'send') {
                    $nativeStatus = (String) $message;
                    $error = (int) $message['err'];

                    $statuses[(String) $message['id_sms']] = [
                        'native_id' => (String) $message['id_sms'],
                        'service_name' => $this->serviceName,
                        'native_status' => $nativeStatus . ($error ? ' / ' . $this->errorMask[$error] : ''),
                        'status' => (isset($this->statusMask[$nativeStatus]) ? $this->statusMask[$nativeStatus] : NotificationLog::STATUS_FAILED),
                        'updated_at' => (String) $message['time'],
                    ];
                }
            }

            if ($statuses) {
                $this->saveLogs($statuses);
            }
        }
    }


    public function checkBalance(): void
    {
        curl_setopt($this->ch, CURLOPT_URL, 'https://adm.redsms.ru/xml/balance.php');
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, '<?xml version="1.0" encoding="utf-8" ?><request><security><login value="' . $this->login . '"/><password value="' . $this->password . '"/></security></request>');
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array("Content-type: text/xml; charset=utf-8"));
        $data = curl_exec($this->ch);

        $xml = new \SimpleXMLElement($data);
        $messages = $xml->children();
        foreach ($messages as $message) {
            if ($message->getName() === 'money')
                $remain = round((float) $message);
        }

        $text = '';
        if (!isset($remain)) {
            $text = 'Проблема с сервисом оповещений redsms.ru';
        } elseif ($remain < 1500) {
            $text = 'Баланс на redsms.ru: '.$remain.' руб.';
        }

        if ($text) {
            $data = [
                'type' => AbstractSender::QUEUE_TYPE_SMS,
                'sms_type' => 13,
                'order_id' => 0,
                'phone' => parent::BALANCE_PHONE,
                'text' => $text,
            ];

            $this->getRabbitService()->publish(json_encode([
                'command' => 'service:sender',
                'args' => [
                    'type' => 'check_balance',
                    'data' => json_encode($data),
                ],
            ]));
        }
    }
}