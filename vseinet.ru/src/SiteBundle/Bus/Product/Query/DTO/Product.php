<?php 

namespace SiteBundle\Bus\Product\Query\DTO;

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
    public $exname;

    /**
     * @Assert\Type(type="string")
     */
    public $categoryId;

    /**
     * @Assert\Type(type="integer")
     */
    public $brandId;

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
     * @Assert\Type(type="integer")
     */
    public $minQuantity;

    /**
     * @Assert\Type(type="string")
     */
    public $model;

    /**
     * @Assert\Type(type="string")
     */
    public $manufacturerLink;

    /**
     * @Assert\Type(type="string")
     */
    public $manualLink;

    /**
     * @Assert\Type(type="string")
     */
    public $description;

    /**
     * @Asset\Type(type="integer")
     */
    public $estimate;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isCanonical = true;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantityInCart = 0;

    /**
     * @Assert\Type(type="boolean")
     */
    public $inFavorites = false;

    /**
     * @Assert\Type(type="array")
     */
    public $details;


    public function __construct(
        $id, 
        $name, 
        $exname,
        $categoryId, 
        $brandId,
        $availability, 
        $price, 
        $priceType, 
        $minQuantity,
        $model,
        $manufacturerLink,
        $manualLink,
        $description,
        $estimate
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->exname = $exname;
        $this->categoryId = $categoryId;
        $this->brandId = $brandId;
        $this->availability = $availability;
        $this->price = $price;
        $this->priceType = $priceType;
        $this->minQuantity = $minQuantity;
        $this->model = $model;
        $this->manufacturerLink = $manufacturerLink;
        $this->manualLink = $manualLink;
        $this->description = $description;
        $this->estimate = $estimate;
    }
}
