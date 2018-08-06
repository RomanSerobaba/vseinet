<?php

namespace RegisterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GoodsReserveRegister
 *
 * @ORM\Table(name="goods_reserve_register")
 * @ORM\Entity(repositoryClass="RegisterBundle\Repository\GoodsReserveRegisterRepository")
 */
class GoodsReserveRegister
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
     * @var int
     *
     * @ORM\Column(name="supply_item_id", type="integer")
     */
    private $supplyItemId;

    /**
     * @var string
     *
     * @ORM\Column(name="goods_condition_code", type="string", length=255)
     */
    private $goodsConditionCode;

    /**
     * @var int|null
     *
     * @ORM\Column(name="geo_room_id", type="integer", nullable=true)
     */
    private $geoRoomId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="order_item_id", type="integer", nullable=true)
     */
    private $orderItemId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="goods_issue_id", type="integer", nullable=true)
     */
    private $goodsIssueId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="equipment_id", type="integer", nullable=true)
     */
    private $equipmentId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="goods_release_id", type="integer", nullable=true)
     */
    private $goodsReleaseId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="delta", type="integer", nullable=true)
     */
    private $delta;

    /**
     * @var string
     *
     * @ORM\Column(name="registrator_type_code", type="string", length=255)
     */
    private $registratorTypeCode;

    /**
     * @var int
     *
     * @ORM\Column(name="registrator_id", type="integer")
     */
    private $registratorId;

    /**
     * @var string
     *
     * @ORM\Column(name="register_operation_type_code", type="string", length=255)
     */
    private $registerOperationTypeCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registered_at", type="datetime")
     */
    private $registeredAt;

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
     * @var int|null
     *
     * @ORM\Column(name="goods_pallet_id", type="integer", nullable=true)
     */
    private $goodsPalletId;

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
     * @return GoodsReserveRegister
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
     * Set supplyItemId.
     *
     * @param int $supplyItemId
     *
     * @return GoodsReserveRegister
     */
    public function setSupplyItemId($supplyItemId)
    {
        $this->supplyItemId = $supplyItemId;

        return $this;
    }

    /**
     * Get supplyItemId.
     *
     * @return int
     */
    public function getSupplyItemId()
    {
        return $this->supplyItemId;
    }

    /**
     * Set goodsConditionCode.
     *
     * @param string $goodsConditionCode
     *
     * @return GoodsReserveRegister
     */
    public function setGoodsConditionCode($goodsConditionCode)
    {
        $this->goodsConditionCode = $goodsConditionCode;

        return $this;
    }

    /**
     * Get goodsConditionCode.
     *
     * @return string
     */
    public function getGoodsConditionCode()
    {
        return $this->goodsConditionCode;
    }

    /**
     * Set geoRoomId.
     *
     * @param int|null $geoRoomId
     *
     * @return GoodsReserveRegister
     */
    public function setGeoRoomId($geoRoomId = null)
    {
        $this->geoRoomId = $geoRoomId;

        return $this;
    }

    /**
     * Get geoRoomId.
     *
     * @return int|null
     */
    public function getGeoRoomId()
    {
        return $this->geoRoomId;
    }

    /**
     * Set orderItemId.
     *
     * @param int|null $orderItemId
     *
     * @return GoodsReserveRegister
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
     * Set goodsIssueId.
     *
     * @param int|null $goodsIssueId
     *
     * @return GoodsReserveRegister
     */
    public function setGoodsIssueId($goodsIssueId = null)
    {
        $this->goodsIssueId = $goodsIssueId;

        return $this;
    }

    /**
     * Get goodsIssueId.
     *
     * @return int|null
     */
    public function getGoodsIssueId()
    {
        return $this->goodsIssueId;
    }

    /**
     * Set equipmentId.
     *
     * @param int|null $equipmentId
     *
     * @return GoodsReserveRegister
     */
    public function setEquipmentId($equipmentId = null)
    {
        $this->equipmentId = $equipmentId;

        return $this;
    }

    /**
     * Get equipmentId.
     *
     * @return int|null
     */
    public function getEquipmentId()
    {
        return $this->equipmentId;
    }

    /**
     * Set goodsReleaseId.
     *
     * @param int|null $goodsReleaseId
     *
     * @return GoodsReserveRegister
     */
    public function setGoodsReleaseId($goodsReleaseId = null)
    {
        $this->goodsReleaseId = $goodsReleaseId;

        return $this;
    }

    /**
     * Get goodsReleaseId.
     *
     * @return int|null
     */
    public function getGoodsReleaseId()
    {
        return $this->goodsReleaseId;
    }

    /**
     * Set delta.
     *
     * @param int|null $delta
     *
     * @return GoodsReserveRegister
     */
    public function setDelta($delta = null)
    {
        $this->delta = $delta;

        return $this;
    }

    /**
     * Get delta.
     *
     * @return int|null
     */
    public function getDelta()
    {
        return $this->delta;
    }

    /**
     * Set registratorTypeCode.
     *
     * @param string $registratorTypeCode
     *
     * @return GoodsReserveRegister
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
     * Set registratorId.
     *
     * @param int $registratorId
     *
     * @return GoodsReserveRegister
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
     * Set registerOperationTypeCode.
     *
     * @param string $registerOperationTypeCode
     *
     * @return GoodsReserveRegister
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
     * Set registeredAt.
     *
     * @param \DateTime $registeredAt
     *
     * @return GoodsReserveRegister
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
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return GoodsReserveRegister
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
     * @return GoodsReserveRegister
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
     * Set goodsPalletId.
     *
     * @param int|null $goodsPalletId
     *
     * @return GoodsReserveRegister
     */
    public function setGoodsPalletId($goodsPalletId = null)
    {
        $this->geoRoomId = $goodsPalletId;

        return $this;
    }

    /**
     * Get goodsPalletId.
     *
     * @return int|null
     */
    public function getGoodsPalletId()
    {
        return $this->goodsPalletId;
    }

    
}
