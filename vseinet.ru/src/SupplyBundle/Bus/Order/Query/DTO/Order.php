<?php 

namespace SupplyBundle\Bus\Order\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Order
{
    /**
     * @Assert\Type(type="array<SupplyBundle\Bus\Order\Query\DTO\OrderProducts>")
     */
    public $products;

    /**
     * @Assert\Type(type="array<SupplyBundle\Bus\Order\Query\DTO\OrderItems>")
     */
    public $orderItems;
}