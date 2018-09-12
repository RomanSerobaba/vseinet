<?php 

namespace AppBundle\Bus\Cart\Query\DTO;

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
     * @Assert\Type(type="float")
     */
    public $discountPercent;

    /**
     * @Assert\Type(type="integer")
     */
    public $priceDiscount;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @Assert\Type(type="array")
     */
    public $points;


    public function __construct($id, $name, $minQuantity, $baseSrc, $price, $discountPercent, $quantity = 0)
    {
        $this->id = $id;
        $this->name = $name;
        $this->minQuantity = $minQuantity;
        $this->baseSrc = $baseSrc;
        $this->price = $price;
        $this->discountPercent = $discountPercent;
        $this->priceDiscount = round($price * (1 - $discountPercent / 100), -2);
        $this->quantity = $quantity;
    }
}
