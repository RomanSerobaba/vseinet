<?php

namespace AppBundle\Bus\Cart\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as VIC;

class ProductInfo
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $price;

    /**
     * @Assert\Type(type="integer")
     */
    public $minQuantity;

    /**
     * @VIC\Enum("AppBundle\Enum\ProductAvailabilityCode")
     */
    public $productAvailabilityCode;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;


    public function __construct($id, $price, $minQuantity, $productAvailabilityCode, $quantity = 0)
    {
        $this->id = $id;
        $this->price = $price;
        $this->minQuantity = $minQuantity;
        $this->productAvailabilityCode = $productAvailabilityCode;
        $this->quantity = $quantity;
    }
}
