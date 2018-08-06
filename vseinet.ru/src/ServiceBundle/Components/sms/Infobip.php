<?php

namespace ServiceBundle\Components\sms;

use ServiceBundle\Components\AbstractSms;
use ServiceBundle\Entity\NotificationLog;
use ServiceBundle\Services\AbstractSender;

class Infobip extends AbstractSms
{
    const SCENARIO_KEY = '84E35850EF972B5F57AAC7EA8CD2EFE9';

    public $path = 'http://api.infobip.com';
    protected $serviceName = NotificationLog::SERVICE_NAME_INFOBIP;
    protected $apiKey = 'dnNlaW5ldDpKYVl3b2s2IQ==';
    protected $key;
    protected $statusMask = [
        'ACCEPTED' => NotificationLog::STATUS_CREATED,
        'PENDING' => NotificationLog::STATUS_PENDING,
        'UNDELIVERABLE' => NotificationLog::STATUS_FAILED,
        'DELIVERED' => NotificationLog::STATUS_DELIVERED,
        'EXPIRED' => NotificationLog::STATUS_FAILED,
        'REJECTED' => NotificationLog::STATUS_FAILED,
    ];

    /**
     * @return bool
     */
    public function send() : bool
    {
        $isSuccess = false;

        if ($this->isActive) {
            foreach ($this->getData() as $key => $curr) {
                $curr['type'] = $this->getSenderType();

                if ($curr['phone'] != parent::DEFECTIVE_PHONE && substr($curr['phone'], 0, 1) == '9') {
                    $tempIds = array();
                    try {
                        $tempIds = $this->saveLogs([
                            [
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
                            ],
                        ]);
                    } catch (\Exception $e) {
                        $this->debug('Save log error: '.$e->getMessage());
                    }

                    $request = [
                        'scenarioKey' => self::SCENARIO_KEY,
                        'destinations' => [
                            [
                                'to' => [
                                    'phoneNumber' => '7'.$curr['phone'],
                                ],
                            ],
                        ],
                        'viber' => [
                            'text' => $curr['text'],
                        ],
                        'sms' => [
                            'text' => $curr['text'],
                        ]
                    ];

                    if (!empty($curr['viber'])) {
                        $request['viber'] = $curr['viber'];
                    }

                    curl_setopt($this->ch, CURLOPT_URL, 'http://api.infobip.com/omni/1/advanced');
                    curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($request));
                    curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Content-type: application/json', 'Authorization: Basic '.$this->apiKey));
                    curl_setopt($this->ch, CURLOPT_HEADER, true);

                    $data = curl_exec($this->ch);

                    switch ($http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE)) {
                        case 200:
                            $isSuccess = true;
                            $header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
                            $data = substr($data, $header_size);
                            $data = json_decode($data, true);
                            $data = reset($data['messages']);

                            try {
                                $this->saveLogs([
                                    [
                                        'id' => array_shift($tempIds),
                                        'temp_id' => null,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'status' => $this->statusMask[$data['status']['groupName']],
                                        'type' => $curr['type'],
                                        'native_status' => $data['status']['name'],
                                        'native_id' => $data['messageId'],
                                        'amount' => 1,
                                    ],
                                ]);
                            } catch (\Exception $e) {
                                $this->debug('Save log error: '.$e->getMessage());
                            }

                            break;
                        default:
                            $this->debug('Unexpected HTTP code: '.$http_code);
                    }
                } else {
                    $this->debug('Defective number: ' . $curr['phone'].' Skipped...');
                }
            }

            parent::send();
        }

        return $isSuccess;
    }

    /**
     * @param array $msgs
     */
    public function checkStatus($msgs = []) : void
    {
        if ($this->isActive) {
            curl_setopt($this->ch, CURLOPT_URL,
                'http://api.infobip.com/omni/1/logs?limit=1000&sentSince=' . date('Y-m-d',
                    strtotime('-24 hours')) . 'T' . date('H:i:s.150%2b01:00', strtotime('-24 hours')));
            curl_setopt($this->ch, CURLOPT_HTTPHEADER,
                array('Content-type: application/json', 'Authorization: Basic '.$this->apiKey));
            curl_setopt($this->ch, CURLOPT_HEADER, true);
            curl_setopt($this->ch, CURLOPT_POST, false);
            $data = curl_exec($this->ch);

            switch ($http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE)) {
                case 200:
                    $header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
                    $data = substr($data, $header_size);
                    $data = json_decode($data, true);
                    $messages = [];

                    foreach ($data['results'] as $curr) {
                        if ($curr['channel'] == NotificationLog::CHANNEL_SMS || !isset($messages[$curr['messageId']])) {
                            $messages[$curr['messageId']] = [
                                'native_id' => $curr['messageId'],
                                'service_name' => $this->serviceName,
                                'native_status' => $curr['status']['name'],
                                'status' => $this->statusMask[$curr['status']['groupName']],
                                'updated_at' => date('Y-m-d H:i:s', strtotime($curr['doneAt'])),
                                'channel' => strtolower($curr['channel']) === NotificationLog::CHANNEL_SMS ? NotificationLog::CHANNEL_SMS : NotificationLog::CHANNEL_VIBER,
                            ];
                        }
                    }

                    try {
                        $this->saveLogs($messages);
                    } catch (\Exception $e) {
                        $this->debug('Save log error: '.$e->getMessage());
                    }

                    break;
                default:
                    $this->debug('Unexpected HTTP code: '.$http_code);
            }
        }
    }

    public function checkBalance() : void
    {
        curl_setopt($this->ch, CURLOPT_URL, 'http://api.infobip.com/account/1/balance');
        curl_setopt($this->ch, CURLOPT_HTTPHEADER,
            array('Content-type: application/json', 'Authorization: Basic '.$this->apiKey));
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_POST, false);
        $data = curl_exec($this->ch);
        $data = json_decode($data, true);

        $text = '';
        if (!isset($data['balance'])) {
            $text = 'Проблема с сервисом оповещений infobip.com';
        } elseif (round($data['balance']) < 2500) {
            $text = 'Баланс на infobip.com: ' . round($data['balance']) . ' руб.';
        }

        if ($text) {
            $data = [
                'type' => AbstractSender::QUEUE_TYPE_EMAIL,
                'body' => [
                    'content' => $text,
                    'contentType' => AbstractSender::CONTENT_TYPE_HTML,
                ],
                'subject' => $text,
                'addresses' => 'djasokolov@gmail.com',
                'from' => [
                    'addresses' => AbstractSender::EMAIL_NOREPLY,
                    'name' => AbstractSender::EMAIL_FROM_NAME,
                ],
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