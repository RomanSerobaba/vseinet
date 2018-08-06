<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductRequest
 *
 * @ORM\Table(name="product_request")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\GoodsRequestRepository")
 */
class GoodsRequest
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_point_id", type="integer")
     */
    private $geoPointId;

    /**
     * @var int
     *
     * @ORM\Column(name="equipment_id", type="integer", nullable=true)
     */
    private $equipmentId;

    /**
     * @var int
     *
     * @ORM\Column(name="goods_issue_id", type="integer", nullable=true)
     */
    private $goodsIssueId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=128)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="order_item_status_code", type="string", length=128)
     */
    private $orderItemStatusCode;

    /**
     * @var string
     *
     * @ORM\Column(name="supplier_reserve", type="string", nullable=true)
     */
    private $supplierReserve;

    /**
     * @var int
     *
     * @ORM\Column(name="supplier_id", type="integer", nullable=false)
     */
    private $supplierId;

    /**
     * @var int
     *
     * @ORM\Column(name="supply_id", type="integer", nullable=true)
     */
    private $supplyId;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_unloaded", type="boolean", nullable=false)
     */
    private $isUnloaded;

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
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return GoodsRequest
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return GoodsRequest
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set geoPointId
     *
     * @param integer $geoPointId
     *
     * @return GoodsRequest
     */
    public function setGeoPointId($geoPointId)
    {
        $this->geoPointId = $geoPointId;

        return $this;
    }

    /**
     * Get geoPointId
     *
     * @return int
     */
    public function getGeoPointId()
    {
        return $this->geoPointId;
    }

    /**
     * Set equipmentId
     *
     * @param integer $equipmentId
     *
     * @return GoodsRequest
     */
    public function setEquipmentId($equipmentId)
    {
        $this->equipmentId = $equipmentId;

        return $this;
    }

    /**
     * Get equipmentId
     *
     * @return int
     */
    public function getEquipmentId()
    {
        return $this->equipmentId;
    }

    /**
     * Set productIssueId
     *
     * @param integer $goodsIssueId
     *
     * @return GoodsRequest
     */
    public function setGoodsIssueId($goodsIssueId)
    {
        $this->goodsIssueId = $goodsIssueId;

        return $this;
    }

    /**
     * Get productIssueId
     *
     * @return int
     */
    public function getGoodsIssueId()
    {
        return $this->goodsIssueId;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return GoodsRequest
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set baseProductId
     *
     * @param integer $baseProductId
     *
     * @return GoodsRequest
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return GoodsRequest
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set orderItemStatusCode
     *
     * @param string $orderItemStatusCode
     *
     * @return GoodsRequest
     */
    public function setOrderItemStatusCode($orderItemStatusCode)
    {
        $this->orderItemStatusCode = $orderItemStatusCode;

        return $this;
    }

    /**
     * Get orderItemStatusCode
     *
     * @return string
     */
    public function getOrderItemStatusCode()
    {
        return $this->orderItemStatusCode;
    }

    /**
     * @return string
     */
    public function getSupplierReserve(): string
    {
        return $this->supplierReserve;
    }

    /**
     * @param string $supplierReserve
     */
    public function setSupplierReserve(string $supplierReserve)
    {
        $this->supplierReserve = $supplierReserve;
    }

    /**
     * @return int
     */
    public function getSupplierId(): int
    {
        return $this->supplierId;
    }

    /**
     * @param int $supplierId
     */
    public function setSupplierId(int $supplierId)
    {
        $this->supplierId = $supplierId;
    }

    /**
     * @return int
     */
    public function getSupplyId(): int
    {
        return $this->supplyId;
    }

    /**
     * @param int $supplyId
     */
    public function setSupplyId(int $supplyId)
    {
        $this->supplyId = $supplyId;
    }

    /**
     * @return bool
     */
    public function isUnloaded(): bool
    {
        return $this->isUnloaded;
    }

    /**
     * @param bool $isUnloaded
     */
    public function setIsUnloaded(bool $isUnloaded)
    {
        $this->isUnloaded = $isUnloaded;
    }
}

