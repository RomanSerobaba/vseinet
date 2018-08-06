<?php 

namespace SupplyBundle\Bus\Suppliers\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Suppliers
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $managerId;

    /**
     * @Assert\Type(type="integer")
     */
    public $goodsQuantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $suppliesQuantity;

    /**
     * @Assert\Type(type="datetime")
     */
    public $orderThresholdTime;

    /**
     * @Assert\Type(type="datetime")
     */
    public $orderDeliveryTime;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isShipping;

    /**
     * Suppliers constructor.
     * @param $id
     * @param $name
     * @param $managerId
     * @param $goodsQuantity
     * @param $suppliesQuantity
     * @param $orderThresholdTime
     * @param $orderDeliveryTime
     * @param $isShipping
     */
    public function __construct($id, $name, $managerId, $goodsQuantity, $suppliesQuantity, $orderThresholdTime, $orderDeliveryTime, $isShipping)
    {
        $this->id = $id;
        $this->name = $name;
        $this->managerId = $managerId;
        $this->goodsQuantity = $goodsQuantity;
        $this->suppliesQuantity = $suppliesQuantity;
        $this->orderThresholdTime = $orderThresholdTime;
        $this->orderDeliveryTime = $orderDeliveryTime;
        $this->isShipping = $isShipping;
    }
}