<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductPricetag
 *
 * @ORM\Table(name="product_pricetag")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductPricetagRepository")
 */
class ProductPricetag
{
    /**
     * @var int
     *
     * @ORM\Column(name="geo_point_id", type="integer")
     * @ORM\Id
     */
    private $geoPointId;

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
     * @ORM\Column(name="price", type="integer")
     */
    private $price;


    /**
     * Set geoPointId.
     *
     * @param int $geoPointId
     *
     * @return ProductPricetag
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
     * Set baseProductId.
     *
     * @param int $baseProductId
     *
     * @return ProductPricetag
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
     * Set price.
     *
     * @param int $price
     *
     * @return ProductPricetag
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
}
