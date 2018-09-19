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
     * @Assert\Type(type="datetime")
     */
    public $createdAt;

    /**
     * @Assert\Type(type="integer")
     */
    public $amount;

    /**
     * @Assert\Type(type="string")
     */
    public $paymentTypeName;

    /**
     * @Enum("AppBundle\Enum\DeliveryTypeCode")
     */
    public $deliveryType;

    /**
     * @Assert\Type(type="string")
     */
    public $deliveryTypeName;

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
     * @Assert\All({
     *     @Assert\Type(type="AppBundle\Bus\Order\Query\DTO\OrderItem")
     * })
     */
    public $items;


    public function __construct(array $order)
    {
        $this->id = $order['id'];
        $this->createdAt = $order['createdAt'];
        $this->amount = 0;
        $this->paymentType = $order['paymentType'] ?? null;
        $this->paymentTypeName = $order['paymentTypeName'] ?? null;
        $this->deliveryType = $order['deliveryType'] ?? null;
        $this->deliveryTypeName = $order['deliveryTypeName'] ?? null;
        $this->username = $order['financialCounteragentName'] ?? null;
        foreach ($order['contacts'] ?? [] as $contact) {
            $this->contacts[] = new Contact(0, $contact['type'], $contact['value']);
        } 
        $this->cityName = $order['cityName'] ?? null;
        $this->address = $order['deliveryAddress'] ?? null; 
        $statuses = [];
        foreach ($order['items'] as $item) {
            $this->items[] = new OrderItem($item);
            switch ($item['statusCode']) {
                case OrderItemStatus::ANNULLED:
                case OrderItemStatus::CANCELED:
                    break;

                default:
                    $this->amount += $item['quantity'] * $item['retailPrice'];
            }
            if (!isset($statuses[$item['statusCode']])) {
                $statuses[$item['statusCode']] = 0;
            }
            $statuses[$item['statusCode']] += 1;
        }
        uasort($statuses, function($count1, $count2) { return $count1 > $count2 ? 1 : -1; });
        $this->statusCode = key($statuses);
        $this->statusCodeName = OrderItemStatus::getName($this->statusCode);
    }
}
