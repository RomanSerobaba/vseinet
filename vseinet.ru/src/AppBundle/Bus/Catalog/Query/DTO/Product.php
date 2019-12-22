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
     * @Assert\Type(type="integer")
     */
    public $competitorPrice;

    /**
     * @VIC\Enum("AppBundle\Enum\ProductPriceTypeCode")
     */
    public $priceTypeCode;

    /**
     * @Assert\Type("string")
     */
    public $priceTypeName;

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

    /**
     * @Assert\Type("integer")
     */
    public $pricetagQuantity;

    /**
     * @Assert\Type("integer")
     */
    public $purchasePrice;

    /**
     * @Assert\Date
     */
    public $deliveryDate;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isManualPrice;

    /**
     * @Assert\DateTime
     */
    public $priceChangedAt;

    /**
     * @Assert\Type("string")
     */
    public $priceChangedBy;

    /**
     * @Assert\Type("string")
     */
    public $sefUrl;

    public function __construct($id, $name, $baseSrc, $availability, $price, $priceTypeCode, $description, $minQuantity, $updatedAt, $pricetagQuantity, $purchasePrice, $competitorPrice,
    $priceChangedAt,
    $priceChangedBy,
    $sefUrl = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->baseSrc = $baseSrc;
        $this->availability = $availability;
        $this->price = $price;
        $this->priceTypeCode = $priceTypeCode;
        $this->description = $description;
        $this->minQuantity = $minQuantity;
        $this->updatedAt = $updatedAt;
        $this->pricetagQuantity = $pricetagQuantity;
        $this->purchasePrice = $purchasePrice ?? null;
        $this->competitorPrice = $competitorPrice ?? null;
        $this->priceChangedAt = $priceChangedAt;
        $this->priceChangedBy = $priceChangedBy;
        $this->sefUrl = $sefUrl;
    }
}
