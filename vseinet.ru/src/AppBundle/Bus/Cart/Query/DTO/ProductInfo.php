<?php

namespace AppBundle\Bus\Cart\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

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
    public $quantity;


    public function __construct($id, $price, $quantity = 0)
    {
        $this->id = $id;
        $this->price = $price;
        $this->quantity = $quantity;
    }
}
