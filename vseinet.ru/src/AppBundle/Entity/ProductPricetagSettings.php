<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductPricetagSettings
 *
 * @ORM\Table(name="product_pricetag_settings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductPricetagSettingsRepository")
 */
class ProductPricetagSettings
{
    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     * @ORM\Id
     */
    private $baseProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_point_id", type="integer")
     * @ORM\Id
     */
    private $geoPointId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="handmade_price", type="integer", nullable=true)
     */
    private $handmadePrice;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="handmade_created_at", type="datetime", nullable=true)
     */
    private $handmadeCreatedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="handmade_created_by", type="integer", nullable=true)
     */
    private $handmadeCreatedBy;

    /**
     * @var string|null
     *
     * @ORM\Column(name="color", type="string", length=20, nullable=true)
     */
    private $color;


    /**
     * Set baseProductId.
     *
     * @param int $baseProductId
     *
     * @return ProductPricetagSettings
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
     * Set geoPointId.
     *
     * @param int $geoPointId
     *
     * @return ProductPricetagSettings
     */
    public function setGeoPointId($geoPointId)
    {
        $this->geoPointId = $geoPointId;

        return $this;
    }

    /**
     * Get geoPointId.
     *
     * @return int
     */
    public function getGeoPointId()
    {
        return $this->geoPointId;
    }

    /**
     * Set handmadePrice.
     *
     * @param int|null $handmadePrice
     *
     * @return ProductPricetagSettings
     */
    public function setHandmadePrice($handmadePrice = null)
    {
        $this->handmadePrice = $handmadePrice;

        return $this;
    }

    /**
     * Get handmadePrice.
     *
     * @return int|null
     */
    public function getHandmadePrice()
    {
        return $this->handmadePrice;
    }

    /**
     * Set handmadeCreatedAt.
     *
     * @param \DateTime|null $handmadeCreatedAt
     *
     * @return ProductPricetagSettings
     */
    public function setHandmadeCreatedAt($handmadeCreatedAt = null)
    {
        $this->handmadeCreatedAt = $handmadeCreatedAt;

        return $this;
    }

    /**
     * Get handmadeCreatedAt.
     *
     * @return \DateTime|null
     */
    public function getHandmadeCreatedAt()
    {
        return $this->handmadeCreatedAt;
    }

    /**
     * Set handmadeCreatedBy.
     *
     * @param int|null $handmadeCreatedBy
     *
     * @return ProductPricetagSettings
     */
    public function setHandmadeCreatedBy($handmadeCreatedBy = null)
    {
        $this->handmadeCreatedBy = $handmadeCreatedBy;

        return $this;
    }

    /**
     * Get handmadeCreatedBy.
     *
     * @return int|null
     */
    public function getHandmadeCreatedBy()
    {
        return $this->handmadeCreatedBy;
    }

    /**
     * Set color.
     *
     * @param string|null $color
     *
     * @return ProductPricetagSettings
     */
    public function setColor($color = null)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return string|null
     */
    public function getColor()
    {
        return $this->color;
    }
}
