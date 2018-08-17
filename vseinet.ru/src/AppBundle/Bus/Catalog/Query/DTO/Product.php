<?php 

namespace AppBundle\Bus\Catalog\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as VIC;

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
     * @Assert\Type(type="string")
     */
    public $baseSrc;

    /**
     * @VIC\Enum("AppBundle\Enum\ProductAvailabilityCode")
     */
    public $availability;

    /**
     * @Assert\Type(type="integer")
     */
    public $price;

    /**
     * @VIC\Enum("AppBundle\Enum\ProductPriceType")
     */
    public $priceType;

    /**
     * @Assert\Type(type="string")
     */
    public $description;

    /**
     * @Assert\Type(type="integer")
     */
    public $minQuantity;

    /**
     * @Assert\Type(type="datetime")
     */
    public $updatedAt;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantityInCart = 0;


    public function __construct($id, $name, $baseSrc, $availability, $price, $priceType, $description, $minQuantity, $updatedAt)
    {
        $this->id = $id;
        $this->name = $name;
        $this->baseSrc = $baseSrc;
        $this->availability = $availability;
        $this->price = $price;
        $this->priceType = $priceType;
        $this->description = $description;
        $this->minQuantity = $minQuantity;
        $this->updatedAt = $updatedAt;
    }
}