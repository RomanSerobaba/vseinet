<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product
{

    const PRODUCT_AVAILABILITY_CODE_OUT_OF_STOCK = 'out_of_stock';
    const PRODUCT_AVAILABILITY_CODE_ON_DEMAND = 'on_demand';
    const PRODUCT_AVAILABILITY_CODE_IN_TRANSIT = 'in_transit';
    const PRODUCT_AVAILABILITY_CODE_AVAILABLE = 'available';

    const PRICE_TYPE_STANDARD = 'standard';
    const PRICE_TYPE_PRICELIST = 'pricelist';
    const PRICE_TYPE_COMPARED = 'compared';
    const PRICE_TYPE_RECOMMENDED = 'recommended';
    const PRICE_TYPE_MANUAL = 'manual';
    const PRICE_TYPE_ULTIMATE = 'ultimate';
    const PRICE_TYPE_TEMPORARY = 'temporary';
    const PRICE_TYPE_SELLOUT = 'sellout';
    const PRICE_TYPE_ORDERED = 'ordered';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer")
     */
    private $geoCityId;

    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;

    /**
     * @var string
     *
     * @ORM\Column(name="product_availability_code", type="string", length=255)
     */
    private $productAvailabilityCode;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="price_type", type="string", length=255, nullable=true)
     */
    private $priceType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="price_time", type="datetime")
     */
    private $priceTime;

    /**
     * @var int
     *
     * @ORM\Column(name="discount_amount", type="integer", nullable=true)
     */
    private $discountAmount;

    /**
     * @var int
     *
     * @ORM\Column(name="offer_percent", type="integer", nullable=true)
     */
    private $offerPercent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified_at", type="datetime", nullable=true)
     */
    private $modifiedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="delivery_tax", type="integer", nullable=true)
     */
    private $deliveryTax;

    /**
     * @var int
     *
     * @ORM\Column(name="rise_tax", type="integer", nullable=true)
     */
    private $riseTax;

    /**
     * @var int
     *
     * @ORM\Column(name="manual_price", type="integer", nullable=true)
     */
    private $manualPrice;

    /**
     * @var int
     *
     * @ORM\Column(name="manual_price_operated_by", type="integer", nullable=true)
     */
    private $manualPriceOperatedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="manual_price_operated_at", type="datetime", nullable=true)
     */
    private $manualPriceOperatedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="ultimate_price", type="integer", nullable=true)
     */
    private $ultimatePrice;

    /**
     * @var int
     *
     * @ORM\Column(name="ultimate_price_operated_by", type="integer", nullable=true)
     */
    private $ultimatePriceOperatedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ultimate_price_operated_at", type="datetime", nullable=true)
     */
    private $ultimatePriceOperatedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="competitor_price", type="integer", nullable=true)
     */
    private $competitorPrice;

    /**
     * @var int
     *
     * @ORM\Column(name="temporary_price", type="integer", nullable=true)
     */
    private $temporaryPrice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="temporary_price_operated_at", type="datetime", nullable=true)
     */
    private $temporaryPriceOperatedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="temporary_price_operated_by", type="integer", nullable=true)
     */
    private $temporaryPriceOperatedBy;

    /**
     * @var int
     *
     * @ORM\Column(name="rating", type="integer")
     */
    private $rating;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set geoCityId
     *
     * @param integer $geoCityId
     *
     * @return Product
     */
    public function setGeoCityId($geoCityId)
    {
        $this->geoCityId = $geoCityId;

        return $this;
    }

    /**
     * Get geoCityId
     *
     * @return int
     */
    public function getGeoCityId()
    {
        return $this->geoCityId;
    }

    /**
     * Set baseProductId
     *
     * @param integer $baseProductId
     *
     * @return Product
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get baseProductId
     *
     * @return int
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Set productAvailabilityCode
     *
     * @param string $productAvailabilityCode
     *
     * @return Product
     */
    public function setProductAvailabilityCode($productAvailabilityCode)
    {
        $this->productAvailabilityCode = $productAvailabilityCode;

        return $this;
    }

    /**
     * Get productAvailabilityCode
     *
     * @return string
     */
    public function getProductAvailabilityCode()
    {
        return $this->productAvailabilityCode;
    }

    /**
     * Set price
     *
     * @param integer $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set priceType
     *
     * @param string $priceType
     *
     * @return Product
     */
    public function setPriceType($priceType)
    {
        $this->priceType = $priceType;

        return $this;
    }

    /**
     * Get priceType
     *
     * @return string
     */
    public function getPriceType()
    {
        return $this->priceType;
    }

    /**
     * Set priceTime
     *
     * @param \DateTime $priceTime
     *
     * @return Product
     */
    public function setPriceTime($priceTime)
    {
        $this->priceTime = $priceTime;

        return $this;
    }

    /**
     * Get priceTime
     *
     * @return \DateTime
     */
    public function getPriceTime()
    {
        return $this->priceTime;
    }

    /**
     * Set discountAmount
     *
     * @param integer $discountAmount
     *
     * @return Product
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    /**
     * Get discountAmount
     *
     * @return int
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * Set offerPercent
     *
     * @param integer $offerPercent
     *
     * @return Product
     */
    public function setOfferPercent($offerPercent)
    {
        $this->offerPercent = $offerPercent;

        return $this;
    }

    /**
     * Get offerPercent
     *
     * @return int
     */
    public function getOfferPercent()
    {
        return $this->offerPercent;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Product
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set modifiedAt
     *
     * @param \DateTime $modifiedAt
     *
     * @return Product
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Get modifiedAt
     *
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Set deliveryTax
     *
     * @param integer $deliveryTax
     *
     * @return Product
     */
    public function setDeliveryTax($deliveryTax)
    {
        $this->deliveryTax = $deliveryTax;

        return $this;
    }

    /**
     * Get deliveryTax
     *
     * @return int
     */
    public function getDeliveryTax()
    {
        return $this->deliveryTax;
    }

    /**
     * Set riseTax
     *
     * @param integer $riseTax
     *
     * @return Product
     */
    public function setRiseTax($riseTax)
    {
        $this->riseTax = $riseTax;

        return $this;
    }

    /**
     * Get riseTax
     *
     * @return int
     */
    public function getRiseTax()
    {
        return $this->riseTax;
    }

    /**
     * Set manualPrice
     *
     * @param integer $manualPrice
     *
     * @return Product
     */
    public function setManualPrice($manualPrice)
    {
        $this->manualPrice = $manualPrice;

        return $this;
    }

    /**
     * Get manualPrice
     *
     * @return int
     */
    public function getManualPrice()
    {
        return $this->manualPrice;
    }

    /**
     * Set manualPriceOperatedBy
     *
     * @param integer $manualPriceOperatedBy
     *
     * @return Product
     */
    public function setManualPriceOperatedBy($manualPriceOperatedBy)
    {
        $this->manualPriceOperatedBy = $manualPriceOperatedBy;

        return $this;
    }

    /**
     * Get manualPriceOperatedBy
     *
     * @return int
     */
    public function getManualPriceOperatedBy()
    {
        return $this->manualPriceOperatedBy;
    }

    /**
     * Set manualPriceOperatedAt
     *
     * @param \DateTime $manualPriceOperatedAt
     *
     * @return Product
     */
    public function setManualPriceOperatedAt($manualPriceOperatedAt)
    {
        $this->manualPriceOperatedAt = $manualPriceOperatedAt;

        return $this;
    }

    /**
     * Get manualPriceOperatedAt
     *
     * @return \DateTime
     */
    public function getManualPriceOperatedAt()
    {
        return $this->manualPriceOperatedAt;
    }

    /**
     * Set ultimatePrice
     *
     * @param integer $ultimatePrice
     *
     * @return Product
     */
    public function setUltimatePrice($ultimatePrice)
    {
        $this->ultimatePrice = $ultimatePrice;

        return $this;
    }

    /**
     * Get ultimatePrice
     *
     * @return int
     */
    public function getUltimatePrice()
    {
        return $this->ultimatePrice;
    }

    /**
     * Set ultimatePriceOperatedBy
     *
     * @param integer $ultimatePriceOperatedBy
     *
     * @return Product
     */
    public function setUltimatePriceOperatedBy($ultimatePriceOperatedBy)
    {
        $this->ultimatePriceOperatedBy = $ultimatePriceOperatedBy;

        return $this;
    }

    /**
     * Get ultimatePriceOperatedBy
     *
     * @return int
     */
    public function getUltimatePriceOperatedBy()
    {
        return $this->ultimatePriceOperatedBy;
    }

    /**
     * Set ultimatePriceOperatedAt
     *
     * @param \DateTime $ultimatePriceOperatedAt
     *
     * @return Product
     */
    public function setUltimatePriceOperatedAt($ultimatePriceOperatedAt)
    {
        $this->ultimatePriceOperatedAt = $ultimatePriceOperatedAt;

        return $this;
    }

    /**
     * Get ultimatePriceOperatedAt
     *
     * @return \DateTime
     */
    public function getUltimatePriceOperatedAt()
    {
        return $this->ultimatePriceOperatedAt;
    }

    /**
     * Set competitorPrice
     *
     * @param integer $competitorPrice
     *
     * @return Product
     */
    public function setCompetitorPrice($competitorPrice)
    {
        $this->competitorPrice = $competitorPrice;

        return $this;
    }

    /**
     * Get competitorPrice
     *
     * @return int
     */
    public function getCompetitorPrice()
    {
        return $this->competitorPrice;
    }

    /**
     * Set temporaryPrice
     *
     * @param integer $temporaryPrice
     *
     * @return Product
     */
    public function setTemporaryPrice($temporaryPrice)
    {
        $this->temporaryPrice = $temporaryPrice;

        return $this;
    }

    /**
     * Get temporaryPrice
     *
     * @return int
     */
    public function getTemporaryPrice()
    {
        return $this->temporaryPrice;
    }

    /**
     * Set temporaryPriceOperatedAt
     *
     * @param \DateTime $temporaryPriceOperatedAt
     *
     * @return Product
     */
    public function setTemporaryPriceOperatedAt($temporaryPriceOperatedAt)
    {
        $this->temporaryPriceOperatedAt = $temporaryPriceOperatedAt;

        return $this;
    }

    /**
     * Get temporaryPriceOperatedAt
     *
     * @return \DateTime
     */
    public function getTemporaryPriceOperatedAt()
    {
        return $this->temporaryPriceOperatedAt;
    }

    /**
     * Set temporaryPriceOperatedBy
     *
     * @param integer $temporaryPriceOperatedBy
     *
     * @return Product
     */
    public function setTemporaryPriceOperatedBy($temporaryPriceOperatedBy)
    {
        $this->temporaryPriceOperatedBy = $temporaryPriceOperatedBy;

        return $this;
    }

    /**
     * Get temporaryPriceOperatedBy
     *
     * @return int
     */
    public function getTemporaryPriceOperatedBy()
    {
        return $this->temporaryPriceOperatedBy;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     *
     * @return Product
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }
}
