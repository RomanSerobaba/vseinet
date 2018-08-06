<?php 

namespace SupplyBundle\Bus\Suppliers\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Supplier
{

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="datetime")
     */
    public $orderThresholdTime;

    /**
     * @Assert\Type(type="datetime")
     */
    public $orderDeliveryTime;

    /**
     * Supplier constructor.
     */
    public function __construct()
    {
    }
}