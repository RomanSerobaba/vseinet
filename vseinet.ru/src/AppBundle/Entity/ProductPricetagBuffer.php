<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductPricetagBuffer.
 *
 * @ORM\Table(name="product_pricetag_buffer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductPricetagBufferRepository")
 */
class ProductPricetagBuffer
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
     * @ORM\Column(name="created_by", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $createdBy;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * Set basePoductId.
     *
     * @param int $baseProductId
     *
     * @return ProductPricetagBuffer
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
     * Set createdBy.
     *
     * @param int $createdBy
     *
     * @return ProductPricetagBuffer
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set quantity.
     *
     * @param int $quantity
     *
     * @return ProductPricetagBuffer
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity.
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}
