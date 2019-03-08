<?php

namespace AdminBundle\Bus\Product\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Enum\ProductPriceType;

class Product
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
     * @Enum("AppBundle\Enum\ProductPriceType")
     */
    public $type;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isManualPrice;

    public function __construct($id, $price, $type)
    {
        $this->id = $id;
        $this->price = $price;
        $this->type = $type;
        $this->isManualPrice = in_array($type, [ProductPriceType::MANUAL, ProductPriceType::ULTIMATE, ProductPriceType::TEMPORARY]);
    }
}
