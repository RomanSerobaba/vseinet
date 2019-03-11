<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product.
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product
{
    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $baseProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $geoCityId;

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
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="manual_price", type="integer", nullable=true)
     */
    private $manualPrice;

    /**
     * @var int
     *
     * @ORM\Column(name="ultimate_price", type="integer", nullable=true)
     */
    private $ultimatePrice;

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
     * @var int
     *
     * @ORM\Column(name="profit", type="integer")
     */
    private $profit;

    /**
     * Set baseProductId.
     *
     * @param int $baseProductId
     *
     * @return Product
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get baseProductId.
     *
     * @return int
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Set geoCityId.
     *
     * @param int $geoCityId
     *
     * @return Product
     */
    public function setGeoCityId($geoCityId)
    {
        $this->geoCityId = $geoCityId;

        return $this;
    }

    /**
     * Get geoCityId.
     *
     * @return int
     */
    public function getGeoCityId()
    {
        return $this->geoCityId;
    }

    /**
     * Set productAvailabilityCode.
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
     * Get productAvailabilityCode.
     *
     * @return string
     */
    public function getProductAvailabilityCode()
    {
        return $this->productAvailabilityCode;
    }

    /**
     * Set price.
     *
     * @param int $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set priceType.
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
     * Get priceType.
     *
     * @return string
     */
    public function getPriceType()
    {
        return $this->priceType;
    }

    /**
     * Set priceTime.
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
     * Get priceTime.
     *
     * @return \DateTime
     */
    public function getPriceTime()
    {
        return $this->priceTime;
    }

    /**
     * Set discountAmount.
     *
     * @param int $discountAmount
     *
     * @return Product
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    /**
     * Get discountAmount.
     *
     * @return int
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * Set createdAt.
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
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set manualPrice.
     *
     * @param int $manualPrice
     *
     * @return Product
     */
    public function setManualPrice($manualPrice)
    {
        $this->manualPrice = $manualPrice;

        return $this;
    }

    /**
     * Get manualPrice.
     *
     * @return int
     */
    public function getManualPrice()
    {
        return $this->manualPrice;
    }

    /**
     * Set ultimatePrice.
     *
     * @param int $ultimatePrice
     *
     * @return Product
     */
    public function setUltimatePrice($ultimatePrice)
    {
        $this->ultimatePrice = $ultimatePrice;

        return $this;
    }

    /**
     * Get ultimatePrice.
     *
     * @return int
     */
    public function getUltimatePrice()
    {
        return $this->ultimatePrice;
    }

    /**
     * Set competitorPrice.
     *
     * @param int $competitorPrice
     *
     * @return Product
     */
    public function setCompetitorPrice($competitorPrice)
    {
        $this->competitorPrice = $competitorPrice;

        return $this;
    }

    /**
     * Get competitorPrice.
     *
     * @return int
     */
    public function getCompetitorPrice()
    {
        return $this->competitorPrice;
    }

    /**
     * Set temporaryPrice.
     *
     * @param int $temporaryPrice
     *
     * @return Product
     */
    public function setTemporaryPrice($temporaryPrice)
    {
        $this->temporaryPrice = $temporaryPrice;

        return $this;
    }

    /**
     * Get temporaryPrice.
     *
     * @return int
     */
    public function getTemporaryPrice()
    {
        return $this->temporaryPrice;
    }

    /**
     * Set profit.
     *
     * @param int $profit
     *
     * @return Product
     */
    public function setProfit($profit)
    {
        $this->profit = $profit;

        return $this;
    }

    /**
     * Get profit.
     *
     * @return int
     */
    public function getProfit()
    {
        return $this->profit;
    }
}
