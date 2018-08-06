<?php

namespace PricingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductPriceLog
 *
 * @ORM\Table(name="product_price_log")
 * @ORM\Entity(repositoryClass="PricingBundle\Repository\ProductPriceLogRepository")
 */
class ProductPriceLog
{
    const PRICE_TYPE_SPECIAL = 'special';
    const PRICE_TYPE_ULTIMATE = 'ultimate';
    const PRICE_TYPE_TEMPORARY = 'temporary';

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
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer")
     */
    private $geoCityId;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer", nullable=true)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="price_type", type="string", length=255)
     */
    private $priceType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="operated_at", type="datetime")
     */
    private $operatedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="operated_by", type="integer", nullable=true)
     */
    private $operatedBy;


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
     * Set baseProductId
     *
     * @param integer $baseProductId
     *
     * @return ProductPriceLog
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
     * Set geoCityId
     *
     * @param integer $geoCityId
     *
     * @return ProductPriceLog
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
     * Set price
     *
     * @param integer $price
     *
     * @return ProductPriceLog
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
     * @return ProductPriceLog
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
     * Set operatedAt
     *
     * @param \DateTime $operatedAt
     *
     * @return ProductPriceLog
     */
    public function setOperatedAt($operatedAt)
    {
        $this->operatedAt = $operatedAt;

        return $this;
    }

    /**
     * Get operatedAt
     *
     * @return \DateTime
     */
    public function getOperatedAt()
    {
        return $this->operatedAt;
    }

    /**
     * Set operatedBy
     *
     * @param integer $operatedBy
     *
     * @return ProductPriceLog
     */
    public function setOperatedBy($operatedBy)
    {
        $this->operatedBy = $operatedBy;

        return $this;
    }

    /**
     * Get operatedBy
     *
     * @return int
     */
    public function getOperatedBy()
    {
        return $this->operatedBy;
    }
}

