<?php
/**
 * Copyright (c) VseInet.ru
 * Author: Kalchenko Sergey
 * Date: 29.03.2019.
 */

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
     * @ORM\Column(name="geo_city_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @var int
     */
    private $geoCityId;

    /**
     * @ORM\Column(name="base_product_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @var int
     */
    private $baseProductId;

    /**
     * @ORM\Column(name="product_availability_code", type="string", length=255)
     *
     * @var string
     */
    private $productAvailabilityCode;

    /**
     * @ORM\Column(name="price", type="integer")
     *
     * @var int
     */
    private $price;

    /**
     * @ORM\Column(name="price_type", type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $priceType;

    /**
     * @ORM\Column(name="price_time", type="datetime")
     *
     * @var \DateTime
     */
    private $priceTime;

    /**
     * @ORM\Column(name="discount_amount", type="integer", nullable=true)
     *
     * @var int
     */
    private $discountAmount;

    /**
     * @ORM\Column(name="offer_percent", type="integer", nullable=true)
     *
     * @var int
     */
    private $offerPercent;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @ORM\Column(name="modified_at", type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $modifiedAt;

    /**
     * @ORM\Column(name="delivery_tax", type="integer", nullable=true)
     *
     * @var int
     */
    private $deliveryTax;

    /**
     * @ORM\Column(name="lifting_tax", type="integer", nullable=true)
     *
     * @var int
     */
    private $liftingTax;

    /**
     * @ORM\Column(name="manual_price", type="integer", nullable=true)
     *
     * @var int
     */
    private $manualPrice;

    /**
     * @ORM\Column(name="manual_price_operated_by", type="integer", nullable=true)
     *
     * @var int
     */
    private $manualPriceOperatedBy;

    /**
     * @ORM\Column(name="manual_price_operated_at", type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $manualPriceOperatedAt;

    /**
     * @ORM\Column(name="ultimate_price", type="integer", nullable=true)
     *
     * @var int
     */
    private $ultimatePrice;

    /**
     * @ORM\Column(name="ultimate_price_operated_by", type="integer", nullable=true)
     *
     * @var int
     */
    private $ultimatePriceOperatedBy;

    /**
     * @ORM\Column(name="ultimate_price_operated_at", type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $ultimatePriceOperatedAt;

    /**
     * @ORM\Column(name="competitor_price", type="integer", nullable=true)
     *
     * @var int
     */
    private $competitorPrice;

    /**
     * @ORM\Column(name="temporary_price", type="integer", nullable=true)
     *
     * @var int
     */
    private $temporaryPrice;

    /**
     * @ORM\Column(name="temporary_price_operated_by", type="integer", nullable=true)
     *
     * @var int
     */
    private $temporaryPriceOperatedBy;

    /**
     * @ORM\Column(name="temporary_price_operated_at", type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $temporaryPriceOperatedAt;

    /**
     * @ORM\Column(name="rating", type="integer", nullable=true)
     *
     * @var int
     */
    private $rating;

    /**
     * @ORM\Column(name="profit", type="integer", nullable=true)
     *
     * @var int
     */
    private $profit;

    /**
     * Set geoCityId.
     *
     * @param int $geoCityId
     *
     * @return $this
     */
    public function setGeoCityId($geoCityId): self
    {
        $this->geoCityId = $geoCityId;

        return $this;
    }

    /**
     * Get geoCityId.
     *
     * @return int
     */
    public function getGeoCityId(): int
    {
        return $this->geoCityId;
    }

    /**
     * Set baseProductId.
     *
     * @param int $baseProductId
     *
     * @return $this
     */
    public function setBaseProductId($baseProductId): self
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get baseProductId.
     *
     * @return int
     */
    public function getBaseProductId(): int
    {
        return $this->baseProductId;
    }

    /**
     * Set productAvailabilityCode.
     *
     * @param string $productAvailabilityCode
     *
     * @return $this
     */
    public function setProductAvailabilityCode($productAvailabilityCode): self
    {
        $this->productAvailabilityCode = $productAvailabilityCode;

        return $this;
    }

    /**
     * Get productAvailabilityCode.
     *
     * @return string
     */
    public function getProductAvailabilityCode(): string
    {
        return $this->productAvailabilityCode;
    }

    /**
     * Set price.
     *
     * @param int $price
     *
     * @return $this
     */
    public function setPrice($price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * Set priceType.
     *
     * @param string $priceType
     *
     * @return $this
     */
    public function setPriceType($priceType): self
    {
        $this->priceType = $priceType;

        return $this;
    }

    /**
     * Get priceType.
     *
     * @return string
     */
    public function getPriceType(): string
    {
        return $this->priceType;
    }

    /**
     * Set priceTime.
     *
     * @param \DateTime|null $priceTime
     *
     * @return $this
     */
    public function setPriceTime($priceTime): self
    {
        $this->priceTime = $priceTime;

        return $this;
    }

    /**
     * Get priceTime.
     *
     * @return \DateTime|null
     */
    public function getPriceTime(): ?\DateTime
    {
        return $this->priceTime;
    }

    /**
     * Set discountAmount.
     *
     * @param int|null $discountAmount
     *
     * @return $this
     */
    public function setDiscountAmount($discountAmount): self
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    /**
     * Get discountAmount.
     *
     * @return int|null
     */
    public function getDiscountAmount(): ?int
    {
        return $this->discountAmount;
    }

    /**
     * Set offerPercent.
     *
     * @param int|null $offerPercent
     *
     * @return $this
     */
    public function setOfferPercent($offerPercent): self
    {
        $this->offerPercent = $offerPercent;

        return $this;
    }

    /**
     * Get offerPercent.
     *
     * @return int|null
     */
    public function getOfferPercent(): ?int
    {
        return $this->offerPercent;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime|null $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set modifiedAt.
     *
     * @param \DateTime|null $modifiedAt
     *
     * @return $this
     */
    public function setModifiedAt($modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Get modifiedAt.
     *
     * @return \DateTime|null
     */
    public function getModifiedAt(): ?\DateTime
    {
        return $this->modifiedAt;
    }

    /**
     * Set deliveryTax.
     *
     * @param int|null $deliveryTax
     *
     * @return $this
     */
    public function setDeliveryTax($deliveryTax): self
    {
        $this->deliveryTax = $deliveryTax;

        return $this;
    }

    /**
     * Get deliveryTax.
     *
     * @return int|null
     */
    public function getDeliveryTax(): ?int
    {
        return $this->deliveryTax;
    }

    /**
     * Set liftingTax.
     *
     * @param int|null $liftingTax
     *
     * @return $this
     */
    public function setLiftingTax($liftingTax): self
    {
        $this->liftingTax = $liftingTax;

        return $this;
    }

    /**
     * Get liftingTax.
     *
     * @return int|null
     */
    public function getLiftingTax(): ?int
    {
        return $this->liftingTax;
    }

    /**
     * Set manualPrice.
     *
     * @param int|null $manualPrice
     *
     * @return $this
     */
    public function setManualPrice($manualPrice): self
    {
        $this->manualPrice = $manualPrice;

        return $this;
    }

    /**
     * Get manualPrice.
     *
     * @return int|null
     */
    public function getManualPrice(): ?int
    {
        return $this->manualPrice;
    }

    /**
     * Set manualPriceOperatedBy.
     *
     * @param int|null $manualPriceOperatedBy
     *
     * @return $this
     */
    public function setManualPriceOperatedBy($manualPriceOperatedBy): self
    {
        $this->manualPriceOperatedBy = $manualPriceOperatedBy;

        return $this;
    }

    /**
     * Get manualPriceOperatedBy.
     *
     * @return int|null
     */
    public function getManualPriceOperatedBy(): ?int
    {
        return $this->manualPriceOperatedBy;
    }

    /**
     * Set manualPriceOperatedAt.
     *
     * @param \DateTime|null $manualPriceOperatedAt
     *
     * @return $this
     */
    public function setManualPriceOperatedAt($manualPriceOperatedAt): self
    {
        $this->manualPriceOperatedAt = $manualPriceOperatedAt;

        return $this;
    }

    /**
     * Get manualPriceOperatedAt.
     *
     * @return \DateTime|null
     */
    public function getManualPriceOperatedAt(): ?\DateTime
    {
        return $this->manualPriceOperatedAt;
    }

    /**
     * Set ultimatePrice.
     *
     * @param int|null $ultimatePrice
     *
     * @return $this
     */
    public function setUltimatePrice($ultimatePrice): self
    {
        $this->ultimatePrice = $ultimatePrice;

        return $this;
    }

    /**
     * Get ultimatePrice.
     *
     * @return int|null
     */
    public function getUltimatePrice(): ?int
    {
        return $this->ultimatePrice;
    }

    /**
     * Set ultimatePriceOperatedBy.
     *
     * @param int|null $ultimatePriceOperatedBy
     *
     * @return $this
     */
    public function setUltimatePriceOperatedBy($ultimatePriceOperatedBy): self
    {
        $this->ultimatePriceOperatedBy = $ultimatePriceOperatedBy;

        return $this;
    }

    /**
     * Get ultimatePriceOperatedBy.
     *
     * @return int|null
     */
    public function getUltimatePriceOperatedBy(): ?int
    {
        return $this->ultimatePriceOperatedBy;
    }

    /**
     * Set ultimatePriceOperatedAt.
     *
     * @param \DateTime|null $ultimatePriceOperatedAt
     *
     * @return $this
     */
    public function setUltimatePriceOperatedAt($ultimatePriceOperatedAt): self
    {
        $this->ultimatePriceOperatedAt = $ultimatePriceOperatedAt;

        return $this;
    }

    /**
     * Get ultimatePriceOperatedAt.
     *
     * @return \DateTime|null
     */
    public function getUltimatePriceOperatedAt(): ?\DateTime
    {
        return $this->ultimatePriceOperatedAt;
    }

    /**
     * Set competitorPrice.
     *
     * @param int|null $competitorPrice
     *
     * @return $this
     */
    public function setCompetitorPrice($competitorPrice): self
    {
        $this->competitorPrice = $competitorPrice;

        return $this;
    }

    /**
     * Get competitorPrice.
     *
     * @return int|null
     */
    public function getCompetitorPrice(): ?int
    {
        return $this->competitorPrice;
    }

    /**
     * Set temporaryPrice.
     *
     * @param int|null $temporaryPrice
     *
     * @return $this
     */
    public function setTemporaryPrice($temporaryPrice): self
    {
        $this->temporaryPrice = $temporaryPrice;

        return $this;
    }

    /**
     * Get temporaryPrice.
     *
     * @return int|null
     */
    public function getTemporaryPrice(): ?int
    {
        return $this->temporaryPrice;
    }

    /**
     * Set temporaryPriceOperatedBy.
     *
     * @param int|null $temporaryPriceOperatedBy
     *
     * @return $this
     */
    public function setTemporaryPriceOperatedBy($temporaryPriceOperatedBy): self
    {
        $this->temporaryPriceOperatedBy = $temporaryPriceOperatedBy;

        return $this;
    }

    /**
     * Get temporaryPriceOperatedBy.
     *
     * @return int|null
     */
    public function getTemporaryPriceOperatedBy(): ?int
    {
        return $this->temporaryPriceOperatedBy;
    }

    /**
     * Set temporaryPriceOperatedAt.
     *
     * @param \DateTime|null $temporaryPriceOperatedAt
     *
     * @return $this
     */
    public function setTemporaryPriceOperatedAt($temporaryPriceOperatedAt): self
    {
        $this->temporaryPriceOperatedAt = $temporaryPriceOperatedAt;

        return $this;
    }

    /**
     * Get temporaryPriceOperatedAt.
     *
     * @return \DateTime|null
     */
    public function getTemporaryPriceOperatedAt(): ?\DateTime
    {
        return $this->temporaryPriceOperatedAt;
    }

    /**
     * Set rating.
     *
     * @param int|null $rating
     *
     * @return $this
     */
    public function setRating($rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating.
     *
     * @return int|null
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    /**
     * Set profit.
     *
     * @param int|null $profit
     *
     * @return $this
     */
    public function setProfit($profit): self
    {
        $this->profit = $profit;

        return $this;
    }

    /**
     * Get profit.
     *
     * @return int|null
     */
    public function getProfit(): ?int
    {
        return $this->profit;
    }
}
