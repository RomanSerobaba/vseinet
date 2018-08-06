<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierOrder
 *
 * @ORM\Table(name="supplier_order")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\SupplierOrderRepository")
 */
class SupplierOrder
{
    const PROCESSING = 'processing';
    const RESERVED = 'reserved';
    const LACK = 'lack';
    const DELAYED = 'delayed';

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
     * @ORM\Column(name="supplier_id", type="integer")
     */
    private $supplierId;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     */
    private $price;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="price_changed_at", type="datetime", nullable=true)
     */
    private $priceChangedAt;


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
     * @return SupplierOrder
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
     * Set supplierId
     *
     * @param integer $supplierId
     *
     * @return SupplierOrder
     */
    public function setSupplierId($supplierId)
    {
        $this->supplierId = $supplierId;

        return $this;
    }

    /**
     * Get supplierId
     *
     * @return int
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice(int $price)
    {
        $this->price = $price;
    }

    /**
     * @return \DateTime
     */
    public function getPriceChangedAt(): \DateTime
    {
        return $this->priceChangedAt;
    }

    /**
     * @param \DateTime $priceChangedAt
     */
    public function setPriceChangedAt(\DateTime $priceChangedAt)
    {
        $this->priceChangedAt = $priceChangedAt;
    }
}

