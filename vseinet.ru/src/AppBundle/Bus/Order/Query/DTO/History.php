<?php

namespace AppBundle\Bus\Order\Query\DTO;

use AppBundle\Enum\OrderItemStatus;
use Symfony\Component\Validator\Constraints as Assert;

class History
{
    /**
     * @Assert\All({
     *     @Assert\Type(type="AppBundle\Bus\Order\Query\DTO\Order")
     * })
     */
    public $orders;

    /**
     * @Assert\Type(type="integer")
     */
    public $total;

    /**
     * @Assert\Type(type="boolean")
     */
    public $canBePayed = false;


    public function __construct(array $history)
    {
        foreach ($history['items'] ?? [] as $order) {
            $this->orders[] = new Order($order);
        }
        $this->total = $history['total'] ?? 0;

        foreach ($history['items'] as $item) {
            if (in_array($item['statusCode'], [OrderItemStatus::COURIER, OrderItemStatus::TRANSIT, OrderItemStatus::RESERVED, OrderItemStatus::SHIPPING, OrderItemStatus::PREPAYABLE, OrderItemStatus::CALLABLE, OrderItemStatus::STATIONED, OrderItemStatus::ARRIVED])) {
                $this->canBePayed = true;
            }
        }

        foreach ($history['items'] as $item) {
            if (OrderItemStatus::CREATED === $item['statusCode']) {
                $this->canBePayed = false;
            }
        }
    }
}
