<?php

namespace ServiceBundle\Components;

use Doctrine\ORM\EntityManager;

class Sms
{
    const MOBILE_OPERATOR_MEGAFON = 'Megafon';
    const MOBILE_OPERATOR_BEELINE = 'Beeline';
    const MOBILE_OPERATOR_MTS = 'Mts';
    const MOBILE_OPERATOR_MISC = 'Misc';

    const SMS_SERVICE_INFOBIP = 'Infobip';
    const SMS_SERVICE_MOBIVISION = 'MobiVision';
    const SMS_SERVICE_NODEPROXY = 'NodeProxy';
    const SMS_SERVICE_REDSMS = 'RedSms';

    const PRIMARY_SMS_SERVICE = self::SMS_SERVICE_INFOBIP;
    const SECONDARY_SMS_SERVICE = self::SMS_SERVICE_REDSMS;

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
     * @return array
     */
    public function getData()
    {
        return $this->_data;
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
     * Sms constructor.
     *
     * @param string $senderType
     * @param array  $data
     */
    public function __construct(string $senderType, array $data)
    {
        $this->_senderType = $senderType;
        $this->_data = $data;
    }

    public function run()
    {
        $result = null;

        if (!empty($this->_data['phone']) && !empty($this->_data['text'])) {
            $smsService = $this->_load($this->_data['phone']);

            $this->debug('$smsService::className() = '.$smsService::className());

            if (!empty($smsService)) {
                $smsService->setSenderType($this->_senderType);
                $smsService->setData([$this->getData(),]);
                $smsService->setEm($this->getEm());
                $smsService->setRabbitService($this->getRabbitService());

                $result = $smsService->send();
            } else {
                $this->debug('Unable to determine sms service by phone ' . $this->_data['phone']);
            }
        } else {
            $this->debug('Empty phone or text: ' . print_r($this->getData(), true));
        }

        return $result;
    }

    /**
     * @param $serviceName
     *
     * @return self
     */
    public function factory($serviceName)
    {
        $serviceName = "ServiceBundle\\Components\\sms\\".$serviceName;

        return new $serviceName();
    }

    /**
     * @param $phone
     *
     * @return AbstractSms
     */
    private function _load($phone)
    {
        $smsService = [
            self::SMS_SERVICE_NODEPROXY => self::SMS_SERVICE_NODEPROXY,
            self::MOBILE_OPERATOR_MTS => self::PRIMARY_SMS_SERVICE,
            self::MOBILE_OPERATOR_BEELINE => self::PRIMARY_SMS_SERVICE,
            self::MOBILE_OPERATOR_MEGAFON => self::PRIMARY_SMS_SERVICE,
            self::MOBILE_OPERATOR_MISC => self::PRIMARY_SMS_SERVICE,
        ];

        $serviceName = $smsService[$this->_getMobileOperator($phone)];

        if (!isset(static::$cash[$serviceName])) {
            static::$cash[$serviceName] = $this->factory($serviceName);
        }

        return static::$cash[$serviceName];
    }

    /**
     * @param $phone
     *
     * @return string
     */
    private function _getMobileOperator($phone) {
        if (!preg_match("~^(\+7|8)*(^9298(0[3-9]|1[12])\d{4})$~isu", $phone) && preg_match("~^(\+7|8)*9(2\d{2}|3([0-37-8]\d|44|66))\d{6}$~isu", $phone)) {
            $result = self::MOBILE_OPERATOR_MEGAFON;
        } elseif (preg_match("~^(\+7|8)*9(0[356]|6[0-8]|5[13])\d{7}$~isu", $phone)) {
            $result = self::MOBILE_OPERATOR_BEELINE;
        } elseif (preg_match("~^(\+7|8)*9[18]\d{8}$~isu", $phone)) {
            $result = self::MOBILE_OPERATOR_MTS;
        } else {
            $result = self::MOBILE_OPERATOR_MISC;
        }

        return $result;
    }

    /**
     * @param $text
     */
    public function debug($text)
    {
        echo '[' . date('H:i:s') . '] ' . $text, PHP_EOL;
    }
}