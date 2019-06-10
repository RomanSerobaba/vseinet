<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Enum\ProductPricetagSize;
use AppBundle\Enum\ProductPricetagColor;

/**
 * Pricetag.
 *
 * @ORM\Table(name="product_pricetag")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PricetagRepository")
 */
class Pricetag
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
     * @ORM\Column(name="geo_point_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $geoPointId;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     */
    private $price;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_handmade", type="boolean")
     */
    private $isHandmade;

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string")
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string")
     */
    private $color;

    public function __construct()
    {
        $this->isHandmade = false;
        $this->size = ProductPricetagSize::A9;
        $this->color = ProductPricetagColor::WHITE;
    }

    /**
     * Set baseProductId.
     *
     * @param int $baseProductId
     *
     * @return Pricetag
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
     * @return Pricetag
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
     * Set price.
     *
     * @param int $price
     *
     * @return Pricetag
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
     * Set isHandmade.
     *
     * @param bool $isHandmade
     *
     * @return Pricetag
     */
    public function setIsHandmade($isHandmade)
    {
        $this->isHandmade = $isHandmade;

        return $this;
    }

    /**
     * Get isHandmade.
     *
     * @return bool
     */
    public function getIsHandmade()
    {
        return $this->isHandmade;
    }

    /**
     * Set size.
     *
     * @param string $size
     *
     * @return Pricetag
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size.
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set color.
     *
     * @param string $color
     *
     * @return Pricetag
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }
}
