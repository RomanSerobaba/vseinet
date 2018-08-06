<?php 

namespace SiteBundle\Bus\Cart\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Product 
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
    public $minQuantity;

    /**
     * @Assert\Type(type="string")
     */
    public $baseSrc;

    /**
     * @Assert\Type(type="integer")
     */
    public $price;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $priceDiscount;

    /**
     * @Assert\Type(type="array")
     */
    public $points;


    public function __construct($id, $name, $minQuantity, $baseSrc, $price, $quantity = 0)
    {
        $this->id = $id;
        $this->name = $name;
        $this->minQuantity = $minQuantity;
        $this->baseSrc = $baseSrc;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->priceDiscount = $price;
    }
}
