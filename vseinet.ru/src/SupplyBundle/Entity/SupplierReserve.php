<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierReserve
 *
 * @ORM\Table(name="supplier_reserve")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\SupplierReserveRepository")
 */
class SupplierReserve
{
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
     * @ORM\Column(name="supplier_id", type="integer")
     */
    private $supplierId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_shipping", type="boolean", nullable=true)
     */
    private $isShipping;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="closed_at", type="datetime", nullable=true)
     */
    private $closedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="closed_by", type="integer", nullable=true)
     */
    private $closedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="order_delivery_time", type="datetime")
     */
    private $orderDeliveryTime;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set supplierId.
     *
     * @param int $supplierId
     *
     * @return SupplierReserve
     */
    public function setSupplierId($supplierId)
    {
        $this->supplierId = $supplierId;

        return $this;
    }

    /**
     * Get supplierId.
     *
     * @return int
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return SupplierReserve
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
     * Set createdBy.
     *
     * @param int|null $createdBy
     *
     * @return SupplierReserve
     */
    public function setCreatedBy($createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return int|null
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set isShipping.
     *
     * @param bool|null $isShipping
     *
     * @return SupplierReserve
     */
    public function setIsShipping($isShipping = null)
    {
        $this->isShipping = $isShipping;

        return $this;
    }

    /**
     * Get isShipping.
     *
     * @return bool|null
     */
    public function getIsShipping()
    {
        return $this->isShipping;
    }

    /**
     * Set closedAt.
     *
     * @param \DateTime|null $closedAt
     *
     * @return SupplierReserve
     */
    public function setClosedAt($closedAt = null)
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    /**
     * Get closedAt.
     *
     * @return \DateTime|null
     */
    public function getClosedAt()
    {
        return $this->closedAt;
    }

    /**
     * Set closedBy.
     *
     * @param int|null $closedBy
     *
     * @return SupplierReserve
     */
    public function setClosedBy($closedBy = null)
    {
        $this->closedBy = $closedBy;

        return $this;
    }

    /**
     * Get closedBy.
     *
     * @return int|null
     */
    public function getClosedBy()
    {
        return $this->closedBy;
    }

    /**
     * Set orderDeliveryTime.
     *
     * @param \DateTime $orderDeliveryTime
     *
     * @return SupplierReserve
     */
    public function setOrderDeliveryTime($orderDeliveryTime)
    {
        $this->orderDeliveryTime = $orderDeliveryTime;

        return $this;
    }

    /**
     * Get orderDeliveryTime.
     *
     * @return \DateTime
     */
    public function getOrderDeliveryTime()
    {
        return $this->orderDeliveryTime;
    }
}
