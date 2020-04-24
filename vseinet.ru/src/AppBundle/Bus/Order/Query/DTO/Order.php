<?php

namespace AppBundle\Bus\Order\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Bus\User\Query\DTO\Contact;
use AppBundle\Enum\OrderItemStatus;

class Order
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $number;

    /**
     * @Assert\Type(type="integer")
     */
    public $financialCounteragentId;

    /**
     * @Assert\Type(type="datetime")
     */
    public $createdAt;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoCityId;

    /**
     * @Assert\Type(type="integer")
     */
    public $amount;

    /**
     * @Assert\Type(type="integer")
     */
    public $prepaymentAmount;

    /**
     * @Assert\Type(type="boolean")
     */
    public $canBePayed = false;

    /**
     * @Enum("AppBundle\Enum\PaymentTypeCode")
     */
    public $paymentTypeCode;

    /**
     * @Assert\Type(type="string")
     */
    public $paymentTypeName;

    /**
     * @Enum("AppBundle\Enum\DeliveryTypeCode", message="Некорректное значение типа доставки")
     */
    public $deliveryType;

    /**
     * @Assert\Type(type="string")
     */
    public $deliveryTypeName;

    /**
     * @Enum("AppBundle\Enum\OrderTypeCode")
     */
    public $typeCode;

    /**
     * @Enum("AppBundle\Enum\OrderItemStatus")
     */
    public $statusCode;

    /**
     * @Assert\Type(type="string")
     */
    public $statusCodeName;

    /**
     * @Assert\Type(type="string")
     */
    public $username;

    /**
     * @Assert\Type(type="string")
     */
    public $addresseename;

    /**
     * @Assert\All({
     *     @Assert\Type(type="AppBundle\Bus\User\Query\DTO\Contact")
     * })
     */
    public $contacts;

    /**
     * @Assert\Type(type="string")
     */
    public $cityName;

    /**
     * @Assert\Type(type="string")
     */
    public $address;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isCancelRequested;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isCancelEnabled = false;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isCallNeeded = false;

    /**
     * @Assert\All({
     *     @Assert\Type(type="AppBundle\Bus\Order\Query\DTO\OrderItem")
     * })
     */
    public $items;

    public function __construct(array $order)
    {
        $this->id = $order['id'];
        $this->number = $order['number'] ?? 0;
        $this->financialCounteragentId = $order['financialCounteragentId'] ?? null;
        $this->createdAt = $order['createdAt'];
        $this->geoPointId = $order['geoPointId'];
        $this->geoCityId = $order['geoCityId'];
        $this->amount = 0;
        $this->paymentTypeCode = $order['paymentTypeCode'] ?? null;
        $this->paymentType = $order['paymentType'] ?? null;
        $this->paymentTypeName = $order['paymentTypeName'] ?? null;
        $this->deliveryType = $order['deliveryType'] ?? null;
        $this->deliveryTypeName = $order['deliveryType'] ?? null;
        $this->username = $order['financialCounteragentName'] ?? null;
        $this->addresseename = $order['personName'] ?? null;
        $this->typeCode = $order['orderTypeCode'] ?? null;
        $this->prepaymentAmount = $order['prepaymentAmount'] ?? 0;
        $this->isCancelRequested = $order['isCancelRequested'] ?? false;
        $this->isCallNeeded = $order['isCallNeeded'] ?? false;
        foreach ($order['contacts'] ?? [] as $contact) {
            $this->contacts[] = new Contact(0, $contact['typeCode'], $contact['value']);
        }
        $this->cityName = $order['geoCityName'] ?? null;
        $this->address = $order['deliveryAddress'] ?? ($order['address'] ?? null);
        $statuses = [];
        $codes = [];
        foreach ($order['items'] as $item) {
            $this->items[] = new OrderItem($item);

            switch ($item['statusCode']) {
                case OrderItemStatus::ANNULLED:
                case OrderItemStatus::CANCELED:
                    break;

                default:
                    $this->amount += $item['quantity'] * ($item['retailPrice'] ?? 0);
            }

            if (!isset($statuses[$item['statusCode']])) {
                $statuses[$item['statusCode']] = 0;
                $codes[$item['statusCode']] = '';
            }

            $codes[$item['statusCode']] = $item['statusCodeName'];
            ++$statuses[$item['statusCode']];

            if (in_array($item['statusCode'], [OrderItemStatus::COURIER, OrderItemStatus::TRANSIT, OrderItemStatus::RESERVED, OrderItemStatus::STATIONED, OrderItemStatus::ARRIVED, OrderItemStatus::SHIPPING, OrderItemStatus::PREPAYABLE, OrderItemStatus::CALLABLE])) {
                $this->canBePayed = true;
            }

            if (in_array($item['statusCode'], [OrderItemStatus::TRANSIT, OrderItemStatus::RESERVED, OrderItemStatus::STATIONED, OrderItemStatus::ARRIVED, OrderItemStatus::CREATED, OrderItemStatus::SHIPPING, OrderItemStatus::PREPAYABLE, OrderItemStatus::CALLABLE])) {
                $this->isCancelEnabled = true;
            }
        }

        foreach ($order['items'] as $item) {
            if (OrderItemStatus::CREATED === $item['statusCode']) {
                $this->canBePayed = false;
            }
        }

        uasort($statuses, function ($count1, $count2) { return $count1 > $count2 ? 1 : -1; });
        $this->statusCode = key($statuses);
        $this->statusCodeName = $codes[$this->statusCode];
    }
}
