<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductPricetag.
 *
 * @ORM\Table(name="product_pricetag")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductPricetagRepository")
 */
class ProductPricetag
{
    /**
     * @ORM\Column(name="base_product_id", type="integer")
     * @ORM\Id
     *
     * @var int
     */
    private $baseProductId;

    /**
     * @ORM\Column(name="geo_point_id", type="integer")
     * @ORM\Id
     *
     * @var int
     */
    private $geoPointId;

    /**
     * @ORM\Column(name="price", type="integer")
     *
     * @var int
     */
    private $price;

    /**
     * @ORM\Column(name="is_handmade", type="boolean")
     *
     * @var bool
     */
    private $isHandmade;

    /**
     * @ORM\Column(name="size", type="string")
     *
     * @var string
     */
    private $size;

    /**
     * @ORM\Column(name="color", type="string")
     *
     * @var string
     */
    private $color;

    /**
     * @ORM\Column(name="printed_by", type="integer")
     *
     * @var int
     */
    private $printedBy;

    /**
     * @ORM\Column(name="printed_at", type="datetime")
     *
     * @var \DateTime
     */
    private $printedAt;

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
     * Set geoPointId.
     *
     * @param int $geoPointId
     *
     * @return $this
     */
    public function setGeoPointId($geoPointId): self
    {
        $this->geoPointId = $geoPointId;

        return $this;
    }

    /**
     * Get geoPointId.
     *
     * @return int
     */
    public function getGeoPointId(): int
    {
        return $this->geoPointId;
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
     * Set size.
     *
     * @param string $size
     *
     * @return $this
     */
    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size.
     *
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * Set color.
     *
     * @param string $color
     *
     * @return $this
     */
    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * Set isHandmade.
     *
     * @param bool $isHandmade
     *
     * @return $this
     */
    public function setIsHandmade(bool $isHandmade): self
    {
        $this->isHandmade = $isHandmade;

        return $this;
    }

    /**
     * Get isHandmade.
     *
     * @return bool
     */
    public function getIsHandmade(): bool
    {
        return $this->isHandmade;
    }

    /**
     * Set printedBy.
     *
     * @param int $printedBy
     *
     * @return $this
     */
    public function setPrintedBy(int $printedBy): self
    {
        $this->printedBy = $printedBy;

        return $this;
    }

    /**
     * Get printedBy.
     *
     * @return int
     */
    public function getPrintedBy(): int
    {
        return $this->printedBy;
    }

    /**
     * Set printedAt.
     *
     * @param \DateTime $printedAt
     *
     * @return $this
     */
    public function setPrintedAt(\DateTime $printedAt): self
    {
        $this->printedAt = $printedAt;

        return $this;
    }

    /**
     * Get printedAt.
     *
     * @return \DateTime
     */
    public function getPrintedAt(): \DateTime
    {
        return $this->printedAt;
    }
}
