<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderItem.
 *
 * @ORM\Table(name="order_item")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrderItemRepository")
 */
class OrderItem
{
    public const ANNULLED_PERCENT = 20;
    public const REQUIRED_PREPAYMENT_PERCENT = 30;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var int
     *
     * @ORM\Column(name="order_did", type="integer")
     */
    public $orderDid;

    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     */
    public $baseProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    public $quantity;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="initial_delivery_time", type="datetime", nullable=true)
     */
    public $initialDeliveryTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    public $createdAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    public $createdBy;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_delayed", type="boolean", nullable=true, options={"default": false})
     */
    public $isDelayed = false;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_promised", type="boolean", nullable=true, options={"default": false})
     */
    public $isPromised = false;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_unavailable", type="boolean", nullable=true, options={"default": false})
     */
    public $isUnavailable = false;

    /**
     * @var int
     *
     * @ORM\Column(name="supplier_id", type="integer", nullable=true)
     */
    public $supplierId;

    /**
     * @var int
     *
     * @ORM\Column(name="supplier_price", type="integer", nullable=true)
     */
    public $supplierPrice;

    /**
     * @var int|null
     *
     * @ORM\Column(name="pid", type="integer", nullable=true)
     */
    public $pid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="is_shipping", type="integer", nullable=true, options={"default": false})
     */
    public $isShipping = false;

    /**
     * @var int|null
     *
     * @ORM\Column(name="franchiser_purchase_price", type="integer", nullable=true)
     */
    public $franchiserPurchasePrice;

    /**
     * @var int|null
     *
     * @ORM\Column(name="initial_franchiser_purchase_price", type="integer", nullable=true)
     */
    public $initialFanchiserPurchasePrice;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="franchiser_purchase_price_updated_at", type="datetime", nullable=true)
     */
    public $franchiserPurchasePriceUpdatedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="franchiser_purchase_price_updated_by", type="integer", nullable=true)
     */
    public $franchiserPurchasePriceUpdatedBy;

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
     * Set orderDid.
     *
     * @param int $orderDid
     *
     * @return OrderItem
     */
    public function setOrderDid($orderDid)
    {
        $this->orderDid = $orderDid;

        return $this;
    }

    /**
     * Get orderDid.
     *
     * @return int
     */
    public function getOrderDid()
    {
        return $this->orderDid;
    }

    /**
     * Set baseProductId.
     *
     * @param int $baseProductId
     *
     * @return OrderItem
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
     * Set quantity.
     *
     * @param int $quantity
     *
     * @return OrderItem
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

    /**
     * Set initialDeliveryTime.
     *
     * @param \DateTime|null $initialDeliveryTime
     *
     * @return OrderItem
     */
    public function setInitialDeliveryTime($initialDeliveryTime = null)
    {
        $this->initialDeliveryTime = $initialDeliveryTime;

        return $this;
    }

    /**
     * Get initialDeliveryTime.
     *
     * @return \DateTime|null
     */
    public function getInitialDeliveryTime()
    {
        return $this->initialDeliveryTime;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return OrderItem
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
     * @return OrderItem
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
     * Get isDelayed.
     *
     * @return bool|null
     */
    public function getIsDelayed()
    {
        return $this->isDelayed;
    }

    /**
     * Set isDelayed.
     *
     * @param bool|null $isDelayed
     *
     * @return OrderItem
     */
    public function setIsDelayed($isDelayed = null)
    {
        $this->isDelayed = $isDelayed;

        return $this;
    }

    /**
     * Get isUnavailable.
     *
     * @return bool|null
     */
    public function getIsUnavailable()
    {
        return $this->isUnavailable;
    }

    /**
     * Set isUnavailable.
     *
     * @param bool|null $isUnavailable
     *
     * @return OrderItem
     */
    public function setIsUnavailable($isUnavailable = null)
    {
        $this->isUnavailable = $isUnavailable;

        return $this;
    }

    /**
     * Get isPromised.
     *
     * @return bool|null
     */
    public function getIsPromised()
    {
        return $this->isPromised;
    }

    /**
     * Set isPromised.
     *
     * @param bool|null $isPromised
     *
     * @return OrderItem
     */
    public function setIsPromised($isPromised = null)
    {
        $this->isPromised = $isPromised;

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
     * Set isShipping.
     *
     * @param bool|null $isShipping
     *
     * @return OrderItem
     */
    public function setIsShipping($isShipping = null)
    {
        $this->isShipping = $isShipping;

        return $this;
    }

    /**
     * Get supplierId.
     *
     * @return int|null
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * Set supplierId.
     *
     * @param int|null $supplierId
     *
     * @return OrderItem
     */
    public function setSupplierId($supplierId = null)
    {
        $this->supplierId = $supplierId;

        return $this;
    }

    /**
     * Get supplierPrice.
     *
     * @return int|null
     */
    public function getSupplierPrice()
    {
        return $this->supplierPrice;
    }

    /**
     * Set supplierPrice.
     *
     * @param int|null $supplierPrice
     *
     * @return OrderItem
     */
    public function setSupplierPrice($supplierPrice = null)
    {
        $this->supplierPrice = $supplierPrice;

        return $this;
    }

    /**
     * Get pid.
     *
     * @return int|null
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set pid.
     *
     * @param int|null $pid
     *
     * @return OrderItem
     */
    public function setPid($pid = null)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Set franchiserPurchasePrice.
     *
     * @param int|null $franchiserPurchasePrice
     *
     * @return OrderItem
     */
    public function setFranchiserPurchasePrice($franchiserPurchasePrice = null)
    {
        $this->franchiserPurchasePrice = $franchiserPurchasePrice;

        return $this;
    }

    /**
     * Get franchiserPurchasePrice.
     *
     * @return int|null
     */
    public function getFranchiserPurchasePrice()
    {
        return $this->franchiserPurchasePrice;
    }

    /**
     * Set initialFranchiserPurchasePrice.
     *
     * @param int|null $initialFranchiserPurchasePrice
     *
     * @return OrderItem
     */
    public function setInitialFranchiserPurchasePrice($initialFranchiserPurchasePrice = null)
    {
        $this->initialFranchiserPurchasePrice = $initialFranchiserPurchasePrice;

        return $this;
    }

    /**
     * Get initialFranchiserPurchasePrice.
     *
     * @return int|null
     */
    public function getInitialFranchiserPurchasePrice()
    {
        return $this->initialFranchiserPurchasePrice;
    }

    /**
     * Set franchiserPurchasePriceUpdatedAt.
     *
     * @param \DateTime|null $franchiserPurchasePriceUpdatedAt
     *
     * @return OrderItem
     */
    public function setFranchiserPurchasePriceUpdatedAt($franchiserPurchasePriceUpdatedAt = null)
    {
        $this->franchiserPurchasePriceUpdatedAt = $franchiserPurchasePriceUpdatedAt;

        return $this;
    }

    /**
     * Get franchiserPurchasePriceUpdatedAt.
     *
     * @return \DateTime|null
     */
    public function getFranchiserPurchasePriceUpdatedAt()
    {
        return $this->franchiserPurchasePriceUpdatedAt;
    }

    /**
     * Set franchiserPurchasePriceUpdatedBy.
     *
     * @param int|null $franchiserPurchasePriceUpdatedBy
     *
     * @return OrderItem
     */
    public function setFranchiserPurchasePriceUpdatedBy($franchiserPurchasePriceUpdatedBy = null)
    {
        $this->franchiserPurchasePriceUpdatedBy = $franchiserPurchasePriceUpdatedBy;

        return $this;
    }

    /**
     * Get franchiserPurchasePriceUpdatedBy.
     *
     * @return int|null
     */
    public function getFranchiserPurchasePriceUpdatedBy()
    {
        return $this->franchiserPurchasePriceUpdatedBy;
    }
}
