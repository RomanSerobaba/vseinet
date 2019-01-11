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
    public $categoryId;

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
     * @VIC\Enum("AppBundle\Enum\ProductAvailabilityCode")
     */
    public $availabilityCode;

    /**
     * @Assert\Type(type="integer")
     */
    public $deliveryTax;

    /**
     * @Assert\Type(type="integer")
     */
    public $liftingCost;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasStroika;

    /**
     * @Assert\Type(type="integer")
     */
    public $reserveQuantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $storePricetag;

    /**
     * @Assert\Type(type="integer")
     */
    public $priceWithDiscount;


    public function __construct($id, $name, $categoryId, $minQuantity, $baseSrc, $price, $availabilityCode, $deliveryTax, $quantity, $hasStroika, $liftingCost, $discountAmount, $reserveQuantity, $storePricetag)
    {
        $this->id = $id;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->minQuantity = $minQuantity;
        $this->baseSrc = $baseSrc;
        $this->price = $price;
        $this->availabilityCode = $availabilityCode;
        $this->deliveryTax = $deliveryTax;
        $this->liftingCost = $liftingCost;
        $this->quantity = $quantity;
        $this->hasStroika = (bool) $hasStroika;
        $this->priceWithDiscount = round($price - $discountAmount);
        $this->reserveQuantity = $reserveQuantity;
        $this->storePricetag = $storePricetag;
    }
}
