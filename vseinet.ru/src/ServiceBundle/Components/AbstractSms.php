<?php

namespace ServiceBundle\Components;

use Doctrine\ORM\EntityManager;
use ServiceBundle\Entity\NotificationLog;

abstract class AbstractSms
{
    const DEFECTIVE_PHONE = '1111111111';
    const BALANCE_PHONE = '9631094114';

    protected $ch;
    protected $path;
    protected $login;
    protected $password;
    protected $serviceName;
    protected $isActive = true;
    protected $sender = 'Vseinet.ru';

    static protected $cash;

    /**
     * Entity Manager
     *
     * @var EntityManager $em
     */
    protected $em;
    protected $rabbitService;
    private $_senderType = '';
    private $_data = [];

    /**
     * AbstractSms constructor.
     */
    public function __construct()
    {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_URL, $this->path);
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 5);
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->_data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @return string
     */
    public function getSenderType(): string
    {
        return $this->_senderType;
    }

    /**
     * @param string $senderType
     */
    public function setSenderType(string $senderType)
    {
        $this->_senderType = $senderType;
    }

    /**
     * @return EntityManager
     */
    public function getEm(): EntityManager
    {
        return $this->em;
    }

    /**
     * @param EntityManager $em
     */
    public function setEm(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return mixed
     */
    public function getRabbitService()
    {
        return $this->rabbitService;
    }

    /**
     * @param mixed $rabbitService
     */
    public function setRabbitService($rabbitService)
    {
        $this->rabbitService = $rabbitService;
    }

    /**
     * @param $serviceName
     *
     * @return self
     */
    public function factory($serviceName) {
        $serviceName = "ServiceBundle\\Components\\sms\\".$serviceName;

        return new $serviceName();
    }

    /**
     * @param $text
     */
    public function debug($text)
    {
        echo '[' . date('H:i:s') . '] ' . $text, PHP_EOL;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function saveLogs(array $data) : array
    {
        $tempIds = [];

        foreach ($data as $smsData) {
            /**
             * @var NotificationLog $model
             */
            $model = null;
            if (!empty($smsData['id'])) {
                $model = $this->getEm()->getRepository(NotificationLog::class)->findOneBy(['id' => $smsData['id'],]);
                unset($smsData['id']);
            } elseif (!empty($smsData['native_id']) && !empty($smsData['service_name'])) {
                $model = $this->getEm()->getRepository(NotificationLog::class)->findOneBy(['native_id' => $smsData['native_id'], 'service_name' => $smsData['service_name'],]);
            }

            if (!$model instanceof NotificationLog) {
                $model = new NotificationLog();
            }

            if (isset($smsData['pid'])) {
                $model->setPid($smsData['pid']);
            }
            if (isset($smsData['created_at'])) {
                $model->setCreatedAt($smsData['created_at']);
            }
            if (isset($smsData['order_id'])) {
                $model->setOrderId($smsData['order_id']);
            }
            if (isset($smsData['channel'])) {
                $model->setChannel($smsData['channel']);
            }
            if (isset($smsData['type'])) {
                $model->setType($smsData['type']);
            }
            if (isset($smsData['addressee'])) {
                $model->setAddressee($smsData['addressee']);
            }
            if (isset($smsData['text'])) {
                $model->setText($smsData['text']);
            }
            if (isset($smsData['service_name'])) {
                $model->setServiceName($smsData['service_name']);
            }
            if (isset($smsData['status'])) {
                $model->setStatus($smsData['status']);
            }
            if (isset($smsData['native_status'])) {
                $model->setNativeStatus($smsData['native_status']);
            }
            if (isset($smsData['native_error'])) {
                $model->setNativeError($smsData['native_error']);
            }
            if (isset($smsData['native_id'])) {
                $model->setNativeId($smsData['native_id']);
            }
            if (isset($smsData['temp_id'])) {
                $model->setTempId($smsData['temp_id']);
            }
            if (isset($smsData['updated_at'])) {
                $model->setUpdatedAt($smsData['updated_at']);
            }
            if (isset($smsData['amount'])) {
                $model->setAmount($smsData['amount']);
            }

            $this->getEm()->persist($model);
            $this->getEm()->flush();

            $tempIds[] = $model->getId();
        }

        return $tempIds;
    }

    /**
     * @param array $message
     *
     * @return string
     */
    protected function generateTempID(array $message) : string
    {
        return 'err_' . time() . '_' . (isset($message['order_id'])?$message['order_id']:'') . '_' . $message['type'];
    }

    abstract public function send() : bool;
    abstract public function checkStatus($msgs) : void;
    abstract public function checkBalance() : void;
}