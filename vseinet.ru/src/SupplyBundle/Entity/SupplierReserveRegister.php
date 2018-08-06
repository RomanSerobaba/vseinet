<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierReserveRegister
 *
 * @ORM\Table(name="supplier_reserve_register")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\SupplierReserveRegisterRepository")
 */
class SupplierReserveRegister
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
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;

    /**
     * @var integer
     *
     * @ORM\Column(name="delta", type="integer")
     */
    private $delta;

    /**
     * @var int
     *
     * @ORM\Column(name="order_item_id", type="integer", nullable=true)
     */
    private $orderItemId;

    /**
     * @var int
     *
     * @ORM\Column(name="supplier_id", type="integer")
     */
    private $supplierId;

    /**
     * @var int
     *
     * @ORM\Column(name="supplier_reserve_id", type="integer")
     */
    private $supplierReserveId;

    /**
     * @var int
     *
     * @ORM\Column(name="supply_id", type="integer")
     */
    private $supplyId;

    /**
     * @var int
     *
     * @ORM\Column(name="purchase_price", type="integer")
     */
    private $purchasePrice;

    /**
     * @var int
     *
     * @ORM\Column(name="registrator_id", type="integer")
     */
    private $registratorId;

    /**
     * @var string
     *
     * @ORM\Column(name="registrator_type_code", type="string", length=255)
     */
    private $registratorTypeCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registered_at", type="datetime")
     */
    private $registeredAt;

    /**
     * @var string
     *
     * @ORM\Column(name="register_operation_type_code", type="string", length=255)
     */
    private $registerOperationTypeCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="created_by", type="integer")
     */
    private $createdBy;


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
     * Set baseProductId.
     *
     * @param int $baseProductId
     *
     * @return SupplierReserveRegister
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
     * Set delta.
     *
     * @param integer $delta
     *
     * @return SupplierReserveRegister
     */
    public function setDelta($delta)
    {
        $this->delta = $delta;

        return $this;
    }

    /**
     * Get delta.
     *
     * @return integer
     */
    public function getDelta()
    {
        return $this->delta;
    }

    /**
     * Set orderItemId.
     *
     * @param int|null $orderItemId
     *
     * @return SupplierReserveRegister
     */
    public function setOrderItemId($orderItemId = null)
    {
        $this->orderItemId = $orderItemId;

        return $this;
    }

    /**
     * Get orderItemId.
     *
     * @return int|null
     */
    public function getOrderItemId()
    {
        return $this->orderItemId;
    }

    /**
     * Set supplierId.
     *
     * @param int $supplierId
     *
     * @return SupplierReserveRegister
     */
    public function setSupplierId(int $supplierId)
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
     * Set supplierReserveId.
     *
     * @param int $supplierReserveId
     *
     * @return SupplierReserveRegister
     */
    public function setSupplierReserveId($supplierReserveId)
    {
        $this->supplierReserveId = $supplierReserveId;

        return $this;
    }

    /**
     * Get supplierReserveId.
     *
     * @return int
     */
    public function getSupplierReserveId()
    {
        return $this->supplierReserveId;
    }

    /**
     * Set supplyId.
     *
     * @param int $supplyId
     *
     * @return SupplierReserveRegister
     */
    public function setSupplyId(int $supplyId)
    {
        $this->supplyId = $supplyId;

        return $this;
    }

    /**
     * Get supplyId.
     *
     * @return int
     */
    public function getSupplyId()
    {
        return $this->supplyId;
    }

    /**
     * Set purchasePrice.
     *
     * @param int $purchasePrice
     *
     * @return SupplierReserveRegister
     */
    public function setPurchasePrice(int $purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;

        return $this;
    }

    /**
     * Get purchasePrice.
     *
     * @return int
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * Set registratorId.
     *
     * @param int $registratorId
     *
     * @return SupplierReserveRegister
     */
    public function setRegistratorId($registratorId)
    {
        $this->registratorId = $registratorId;

        return $this;
    }

    /**
     * Get registratorId.
     *
     * @return int
     */
    public function getRegistratorId()
    {
        return $this->registratorId;
    }

    /**
     * Set registratorTypeCode.
     *
     * @param string $registratorTypeCode
     *
     * @return SupplierReserveRegister
     */
    public function setRegistratorTypeCode($registratorTypeCode)
    {
        $this->registratorTypeCode = $registratorTypeCode;

        return $this;
    }

    /**
     * Get registratorTypeCode.
     *
     * @return string
     */
    public function getRegistratorTypeCode()
    {
        return $this->registratorTypeCode;
    }

    /**
     * Set registeredAt.
     *
     * @param \DateTime $registeredAt
     *
     * @return SupplierReserveRegister
     */
    public function setRegisteredAt($registeredAt)
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }

    /**
     * Get registeredAt.
     *
     * @return \DateTime
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    /**
     * Set registerOperationTypeCode.
     *
     * @param string $registerOperationTypeCode
     *
     * @return SupplierReserveRegister
     */
    public function setRegisterOperationTypeCode($registerOperationTypeCode)
    {
        $this->registerOperationTypeCode = $registerOperationTypeCode;

        return $this;
    }

    /**
     * Get registerOperationTypeCode.
     *
     * @return string
     */
    public function getRegisterOperationTypeCode()
    {
        return $this->registerOperationTypeCode;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return SupplierReserveRegister
     */
    public function setCreatedAt(\DateTime $createdAt)
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
     * @param int $createdBy
     *
     * @return SupplierReserveRegister
     */
    public function setCreatedBy(int $createdBy)
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
}
