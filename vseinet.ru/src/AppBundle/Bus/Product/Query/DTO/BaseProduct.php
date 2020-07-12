<?php

namespace AppBundle\Bus\Product\Query\DTO;

use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\Enum\ProductPriceTypeCode;
use AppBundle\Validator\Constraints as VIC;
use Symfony\Component\Validator\Constraints as Assert;

class BaseProduct
{
    /**
     * @Assert\Type("integer")
     */
    public $id;

    /**
     * @Assert\Type("string")
     */
    public $name;

    /**
     * @Assert\Type("string")
     */
    public $exname;

    /**
     * @Assert\Type("string")
     */
    public $categoryId;

    /**
     * @Assert\Type("integer")
     */
    public $brandId;

    /**
     * @VIC\Enum("AppBundle\Enum\ProductAvailabilityCode")
     */
    public $availability;

    /**
     * @Assert\Type("integer")
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
     * @Assert\Type("integer")
     */
    public $minQuantity;

    /**
     * @Assert\Type("string")
     */
    public $model;

    /**
     * @Assert\Type("string")
     */
    public $manufacturerLink;

    /**
     * @Assert\Type("string")
     */
    public $manualLink;

    /**
     * @Assert\Type("string")
     */
    public $description;

    /**
     * @Asset\Type("integer")
     */
    public $estimate;

    /**
     * @Assert\Type("integer")
     */
    public $canonicalId;

    /**
     * @Assert\Type("integer")
     */
    public $quantityInCart = 0;

    /**
     * @Assert\Type("boolean")
     */
    public $inFavorites = false;

    /**
     * @Assert\Type("array")
     */
    public $details;

    /**
     * @Assert\Type("integer")
     */
    public $pricetagQuantity;

    /**
     * @Assert\Type("integer")
     */
    public $purchasePrice;

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
     * @Assert\Type("boolean")
     */
    public $isHidden;

    /**
     * @Assert\Type("string")
     */
    public $sefUrl;

    /**
     * @Assert\Type("string")
     */
    public $categorySefUrl;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isBurningOffer;

    public function __construct(
        $id,
        $name,
        $exname,
        $categoryId,
        $brandId,
        $availability,
        $price,
        $priceTypeCode,
        $minQuantity,
        $model,
        $manufacturerLink,
        $manualLink,
        $description,
        $estimate,
        $canonicalId,
        $pricetagQuantity,
        $purchasePrice,
        $competitorPrice,
        $priceChangedAt,
        $priceChangedBy,
        $isHidden,
        $sefUrl = null,
        $categorySefUrl = null,
        $isBurningOffer = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->exname = $exname;
        $this->categoryId = $categoryId;
        $this->brandId = $brandId;
        $this->availability = $isHidden ? ProductAvailabilityCode::OUT_OF_STOCK : $availability;
        $this->price = $price;
        $this->priceTypeCode = $priceTypeCode;
        $this->minQuantity = $minQuantity;
        $this->model = $model;
        $this->manufacturerLink = $manufacturerLink;
        $this->manualLink = $manualLink;
        $this->description = $description;
        $this->estimate = $estimate;
        $this->canonicalId = $canonicalId;
        $this->pricetagQuantity = $pricetagQuantity;
        $this->purchasePrice = $purchasePrice;
        $this->competitorPrice = $competitorPrice;
        $this->isBurningOffer = (bool) $isBurningOffer;
        $this->isManualPrice = in_array($priceTypeCode, [ProductPriceTypeCode::MANUAL, ProductPriceTypeCode::ULTIMATE, ProductPriceTypeCode::TEMPORARY]);
        $this->priceChangedAt = $priceChangedAt;
        $this->priceChangedBy = $priceChangedBy;
        $this->isHidden = $isHidden;
        $this->sefUrl = $sefUrl;
        $this->categorySefUrl = $categorySefUrl;
    }
}
