<?php

namespace ServiceBundle\Services;

use Doctrine\ORM\EntityManager;
use OrderBundle\Entity\OrderItemStatus;

abstract class AbstractSender
{
    const CONTENT_TYPE_HTML = 'text/html';
    const DEFAULT_FROM = 'mail@vseinet.ru';
    const NO_REPLY_MAIL = 'noreply@vseinet.ru';
    const SHOP_ADDRESS = 'vseinet.ru';
    const DEFAULT_MANAGER_ID = 1813;
    const DEFAULT_MANAGER_PHONE = '290708';
    const DELIVERY_PRICE = '300 р.';
    const DELIVERY_SERVICE_PHONE = '220212';

    const QUEUE_TYPE_SMS = 'sms';
    const QUEUE_TYPE_EMAIL = 'email';

    const EMAIL_NOREPLY = 'noreply@' . self::SHOP_ADDRESS;
    const EMAIL_BUH = 'buh@' . self::SHOP_ADDRESS;
    const EMAIL_MAIL = 'mail@' . self::SHOP_ADDRESS;
    const EMAIL_FROM_NAME = self::SHOP_ADDRESS . ', интернет-магазин';
    const EMAIL_ADMIN = 'mail@' . self::SHOP_ADDRESS;

    protected $isEmulate = false;
    private $_timeout = 0; //sec
    private $_queue = [];

    /**
     * Entity Manager
     *
     * @var EntityManager
     */
    private $_em;
    private $_twig;
    private $_rabbit;

    /**
     * @param EntityManager $em
     * @param               $twig
     * @param               $rabbit
     */
    public function configure(EntityManager $em, $twig, $rabbit) : void
    {
        $this->_em = $em;
        $this->_twig = $twig;
        $this->_rabbit = $rabbit;
    }

    /**
     * @return EntityManager
     */
    public function getEm() : EntityManager
    {
        return $this->_em;
    }

    /**
     * @return mixed
     */
    public function getTwig()
    {
        return $this->_twig;
    }

    /**
     * @return mixed
     */
    public function getRabbit()
    {
        return $this->_rabbit;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->_timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout(int $timeout) : void
    {
        $this->_timeout = $timeout;
    }

    /**
     * @return array
     */
    public function getQueue(): array
    {
        return $this->_queue;
    }

    /**
     * @param array $row
     */
    public function appendQueue(array $row) : void
    {
        $this->_queue[] = $row;
    }

    /**
     * @return bool
     */
    public function isEmulate(): bool
    {
        return $this->isEmulate;
    }

    /**
     * @param bool $isEmulate
     */
    public function setIsEmulate(bool $isEmulate)
    {
        $this->isEmulate = $isEmulate;
    }

    /**
     * @return array
     */
    protected function getSmsTemplate() : array
    {
        return [
            'type' => self::QUEUE_TYPE_SMS,
            'sms_type' => 0,
            'order_id' => 0,
            'phone' => '',
            'text' => '',
            'viber' => [],
            'manager' => [
                'phone' => self::DEFAULT_MANAGER_PHONE,
                'firstname' => '',
            ],
            'class' => get_called_class(),
        ];
    }

    /**
     * @return array
     */
    protected function getEmailTemplate() : array
    {
        return [
            'type' => self::QUEUE_TYPE_EMAIL,
            'body' => '',
            'subject' => '',
            'addresses' => '',
            'file' => '',
            'from' => [
                'addresses' => self::EMAIL_NOREPLY,
                'name' => self::EMAIL_FROM_NAME,
            ],
            'class' => get_called_class(),
        ];
    }

    /**
     * @param string $method
     *
     * @return array
     */
    public function publish(string $method) : array
    {
        $response = [
            'ok' => false,
            'data' => [],
            'error' => null,
        ];

        $queue = $this->getQueue();

        if (empty($queue)) {
            $response['error'] = 'Очередь пуста';

            return $response;
        }

        if ($this->isEmulate()) {
            return $queue;
        }

        try {
            foreach ($queue as $item) {
                $item['time'] = time() + $this->getTimeout();

                $this->getRabbit()->publish(json_encode([
                    'command' => 'service:sender',
                    'args' => [
                        'type' => $method,
                        'data' => json_encode($item),
                    ],
                ]));

                $response['data'][] = $item;
            }

            $response['ok'] = true;
        } catch (\Exception $e) {
            $response['error'] = print_r($e, true);
        }

        return $response;
    }

    /**
     * @param int  $representativeID
     * @param int  $orderID
     * @param bool $isFull
     *
     * @return array
     */
    protected function getPointInfo(int $representativeID, int $orderID, bool $isFull) : array
    {
        $info =  [];
        $info['city'] = 'Пенза';
        $info['point'] = 'Рембыттехника';
        $info['address'] = 'ул. Суворова, 225';

        $info['img'] = 'http://' . $_SERVER['SERVER_NAME']. '/u/representatives/141_preview.jpg';
        $imagePath = __DIR__.DIRECTORY_SEPARATOR.'..'.
            DIRECTORY_SEPARATOR.'..'.
            DIRECTORY_SEPARATOR.'..'.
            DIRECTORY_SEPARATOR.'web'.
            DIRECTORY_SEPARATOR.'u'.
            DIRECTORY_SEPARATOR.'representatives'.
            DIRECTORY_SEPARATOR.$representativeID.'_preview.jpg';

        if (file_exists($imagePath)) {
            $info['img'] = 'http://' . $_SERVER['SERVER_NAME']. '/u/representatives/'.$representativeID.'_preview.jpg';
        }

        if (!$isFull) {
            $info['positions'] =  $this->getPositionsByOrderID($orderID);
        }

        return $info;
    }

    /**
     * @param int $orderID
     *
     * @return array
     */
    public function getPositionsByOrderID(int $orderID) : array
    {
        $sql = '
            SELECT
                bp.id, bp.name, oi.quantity
            FROM 
                order_item oi 
                INNER JOIN "order" o ON o.id=oi.order_id 
                INNER JOIN base_product bp ON bp.id=oi.base_product_id 
            WHERE 
                o.id=:id AND oi.order_item_status_code = :status
        ';

        $q = $this->getEm()->createQuery($sql);

        $q->setParameter('id', $orderID);
        $q->setParameter('status', OrderItemStatus::CODE_ARRIVED);

        return $q->getArrayResult();
    }

    /**
     * @param string $view
     * @param array  $parameters
     *
     * @return string
     */
    protected function getEmailBodyFromTemplate(string $view, $parameters = []) : string
    {
        if ($this->_twig) {
            return $this->_twig->render($view, $parameters);
        } else {
            throw new \LogicException('You can not use the "render" method if the Templating Component or the Twig Bundle are not available.');
        }
    }

    abstract public function process(array $data) : void;
    protected function sms(array $data) : void {}
    protected function email(array $data) : void {}
}