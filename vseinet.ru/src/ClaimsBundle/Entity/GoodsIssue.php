<?php

namespace ClaimsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GoodsIssue
 *
 * @ORM\Table(name="goods_issue")
 * @ORM\Entity(repositoryClass="ClaimsBundle\Repository\GoodsIssueRepository")
 */
class GoodsIssue
{
    const CLIENT_DECISION_RETURNED_MONEY = 'returned_money'; // Вернули деньги
    const CLIENT_DECISION_RETURNED_GOODS = 'returned_goods'; // Вернули товар

    const GOODS_DECISION_RETURNED_TO_CLIENT = 'returned_to_client'; // Вернули клиенту
    const GOODS_DECISION_REMOVED_FROM_BALANCE = 'removed_from_balance'; // Сняли с баланса
    const GOODS_DECISION_RETURNED_ON_BALANCE = 'returned_on_balance'; // Вернули на баланс

    const CLIENT_REQUIREMENT_DIAGNOSTICS = 'diagnostics'; // Диагностика
    const CLIENT_REQUIREMENT_REPAIR = 'repair'; // Ремонт
    const CLIENT_REQUIREMENT_REFUND = 'refund'; // Возврат средств

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
     * @var string
     *
     * @ORM\Column(name="quantity", type="string", length=255)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="goods_issue_type_code", type="string", length=255)
     */
    private $goodsIssueTypeCode;

    /**
     * @var int|null
     *
     * @ORM\Column(name="order_item_id", type="integer", nullable=true)
     */
    private $orderItemId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="equipment_id", type="integer", nullable=true)
     */
    private $equipmentId;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="closed_at", type="datetime", nullable=true)
     */
    private $closedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_room_id", type="integer")
     */
    private $geoRoomId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="supplier_decision", type="string", length=255, nullable=true)
     */
    private $supplierDecision;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="supplier_decided_at", type="datetime", nullable=true)
     */
    private $supplierDecidedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="supplier_decided_by", type="integer", nullable=true)
     */
    private $supplierDecidedBy;

    /**
     * @var int|null
     *
     * @ORM\Column(name="supplier_id", type="integer", nullable=true)
     */
    private $supplierId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="supplier_compensation", type="integer", nullable=true)
     */
    private $supplierCompensation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="client_decision", type="string", length=255, nullable=true)
     */
    private $clientDecision;

    /**
     * @var string|null
     *
     * @ORM\Column(name="client_decision_comment", type="string", nullable=true)
     */
    private $clientDecisionComment;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="client_decided_at", type="datetime", nullable=true)
     */
    private $clientDecidedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="client_decided_by", type="integer", nullable=true)
     */
    private $clientDecidedBy;

    /**
     * @var int|null
     *
     * @ORM\Column(name="client_penalty", type="integer", nullable=true)
     */
    private $clientPenalty;

    /**
     * @var string|null
     *
     * @ORM\Column(name="goods_decision", type="string", length=255, nullable=true)
     */
    private $goodsDecision;

    /**
     * @var string|null
     *
     * @ORM\Column(name="goods_decision_comment", type="string", nullable=true)
     */
    private $goodsDecisionComment;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="goods_decided_at", type="datetime", nullable=true)
     */
    private $goodsDecidedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="goods_decided_by", type="integer", nullable=true)
     */
    private $goodsDecidedBy;

    /**
     * @var int|null
     *
     * @ORM\Column(name="product_resort_id", type="integer", nullable=true)
     */
    private $productResortId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="shipment_issue_id", type="integer", nullable=true)
     */
    private $shipmentIssueId;

    /**
     * Комплектация товара: полная/нет
     *
     * @var bool|null
     *
     * @ORM\Column(name="is_full_complete_set", type="boolean", nullable=true)
     */
    private $isFullCompleteSet;

    /**
     * Подробности при неполной комплектации (чего именно не хвататет)
     *
     * @var string|null
     *
     * @ORM\Column(name="complete_set_details", type="string", length=255, nullable=true)
     */
    private $completeSetDetails;

    /**
     * Состояние товара
     *
     * @var bool|null
     *
     * @ORM\Column(name="is_new_goods_condition", type="boolean", nullable=true)
     */
    private $isNewGoodsCondition;

    /**
     * @var string|null
     *
     * @ORM\Column(name="goods_condition_details", type="string", length=255, nullable=true)
     */
    private $goodsConditionDetails;

    /**
     * @var string|null
     *
     * @ORM\Column(name="client_requirement", type="string", length=255, nullable=true)
     */
    private $clientRequirement;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="accepted_at", type="datetime", nullable=true)
     */
    private $acceptedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="accepted_by", type="integer", nullable=true)
     */
    private $acceptedBy;

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
     * @return GoodsIssue
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
     * @param string $quantity
     *
     * @return GoodsIssue
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity.
     *
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set goodsIssueTypeCode.
     *
     * @param string $goodsIssueTypeCode
     *
     * @return GoodsIssue
     */
    public function setGoodsIssueTypeCode($goodsIssueTypeCode)
    {
        $this->goodsIssueTypeCode = $goodsIssueTypeCode;

        return $this;
    }

    /**
     * Get goodsIssueTypeCode.
     *
     * @return string
     */
    public function getGoodsIssueTypeCode()
    {
        return $this->goodsIssueTypeCode;
    }

    /**
     * Set orderItemId.
     *
     * @param int|null $orderItemId
     *
     * @return GoodsIssue
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
     * Set equipmentId.
     *
     * @param int|null $equipmentId
     *
     * @return GoodsIssue
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
     * Set createdAt.
     *
     * @param \DateTime|null $createdAt
     *
     * @return GoodsIssue
     */
    public function setCreatedAt($createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime|null
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
     * @return GoodsIssue
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
     * Set description.
     *
     * @param string|null $description
     *
     * @return GoodsIssue
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set isClosed.
     *
     * @param \DateTime|null $closedAt
     *
     * @return GoodsIssue
     */
    public function setClosedAt($closedAt = null)
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    /**
     * Get isClosed.
     *
     * @return \DateTime|null
     */
    public function getClosedAt()
    {
        return $this->closedAt;
    }

    /**
     * Set geoRoomId.
     *
     * @param int $geoRoomId
     *
     * @return GoodsIssue
     */
    public function setGeoRoomId($geoRoomId)
    {
        $this->geoRoomId = $geoRoomId;

        return $this;
    }

    /**
     * Get geoRoomId.
     *
     * @return int
     */
    public function getGeoRoomId()
    {
        return $this->geoRoomId;
    }

    /**
     * Set supplierDecision.
     *
     * @param string|null $supplierDecision
     *
     * @return GoodsIssue
     */
    public function setSupplierDecision($supplierDecision = null)
    {
        $this->supplierDecision = $supplierDecision;

        return $this;
    }

    /**
     * Get supplierDecision.
     *
     * @return string|null
     */
    public function getSupplierDecision()
    {
        return $this->supplierDecision;
    }

    /**
     * Set supplierDecidedAt.
     *
     * @param \DateTime|null $supplierDecidedAt
     *
     * @return GoodsIssue
     */
    public function setSupplierDecidedAt($supplierDecidedAt = null)
    {
        $this->supplierDecidedAt = $supplierDecidedAt;

        return $this;
    }

    /**
     * Get supplierDecidedAt.
     *
     * @return \DateTime|null
     */
    public function getSupplierDecidedAt()
    {
        return $this->supplierDecidedAt;
    }

    /**
     * Set supplierDecidedBy.
     *
     * @param int|null $supplierDecidedBy
     *
     * @return GoodsIssue
     */
    public function setSupplierDecidedBy($supplierDecidedBy = null)
    {
        $this->supplierDecidedBy = $supplierDecidedBy;

        return $this;
    }

    /**
     * Get supplierDecidedBy.
     *
     * @return int|null
     */
    public function getSupplierDecidedBy()
    {
        return $this->supplierDecidedBy;
    }

    /**
     * Set supplierId.
     *
     * @param int|null $supplierId
     *
     * @return GoodsIssue
     */
    public function setSupplierId($supplierId = null)
    {
        $this->supplierId = $supplierId;

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
     * Set supplierCompensation.
     *
     * @param int|null $supplierCompensation
     *
     * @return GoodsIssue
     */
    public function setSupplierCompensation($supplierCompensation = null)
    {
        $this->supplierCompensation = $supplierCompensation;

        return $this;
    }

    /**
     * Get supplierCompensation.
     *
     * @return int|null
     */
    public function getSupplierCompensation()
    {
        return $this->supplierCompensation;
    }

    /**
     * Set clientDecision.
     *
     * @param string|null $clientDecision
     *
     * @return GoodsIssue
     */
    public function setClientDecision($clientDecision = null)
    {
        $this->clientDecision = $clientDecision;

        return $this;
    }

    /**
     * Get clientDecision.
     *
     * @return string|null
     */
    public function getClientDecision()
    {
        return $this->clientDecision;
    }

    /**
     * Set clientDecidedAt.
     *
     * @param \DateTime|null $clientDecidedAt
     *
     * @return GoodsIssue
     */
    public function setClientDecidedAt($clientDecidedAt = null)
    {
        $this->clientDecidedAt = $clientDecidedAt;

        return $this;
    }

    /**
     * Get clientDecidedAt.
     *
     * @return \DateTime|null
     */
    public function getClientDecidedAt()
    {
        return $this->clientDecidedAt;
    }

    /**
     * Set clientDecidedBy.
     *
     * @param int|null $clientDecidedBy
     *
     * @return GoodsIssue
     */
    public function setClientDecidedBy($clientDecidedBy = null)
    {
        $this->clientDecidedBy = $clientDecidedBy;

        return $this;
    }

    /**
     * Get clientDecidedBy.
     *
     * @return int|null
     */
    public function getClientDecidedBy()
    {
        return $this->clientDecidedBy;
    }

    /**
     * Set clientPenalty.
     *
     * @param int|null $clientPenalty
     *
     * @return GoodsIssue
     */
    public function setClientPenalty($clientPenalty = null)
    {
        $this->clientPenalty = $clientPenalty;

        return $this;
    }

    /**
     * Get clientPenalty.
     *
     * @return int|null
     */
    public function getClientPenalty()
    {
        return $this->clientPenalty;
    }

    /**
     * Set goodsDecision.
     *
     * @param string|null $goodsDecision
     *
     * @return GoodsIssue
     */
    public function setGoodsDecision($goodsDecision = null)
    {
        $this->goodsDecision = $goodsDecision;

        return $this;
    }

    /**
     * Get goodsDecision.
     *
     * @return string|null
     */
    public function getGoodsDecision()
    {
        return $this->goodsDecision;
    }

    /**
     * Set goodsDecidedAt.
     *
     * @param \DateTime|null $goodsDecidedAt
     *
     * @return GoodsIssue
     */
    public function setGoodsDecidedAt($goodsDecidedAt = null)
    {
        $this->goodsDecidedAt = $goodsDecidedAt;

        return $this;
    }

    /**
     * Get goodsDecidedAt.
     *
     * @return \DateTime|null
     */
    public function getGoodsDecidedAt()
    {
        return $this->goodsDecidedAt;
    }

    /**
     * Set goodsDecidedBy.
     *
     * @param int|null $goodsDecidedBy
     *
     * @return GoodsIssue
     */
    public function setGoodsDecidedBy($goodsDecidedBy = null)
    {
        $this->goodsDecidedBy = $goodsDecidedBy;

        return $this;
    }

    /**
     * Get goodsDecidedBy.
     *
     * @return int|null
     */
    public function getGoodsDecidedBy()
    {
        return $this->goodsDecidedBy;
    }

    /**
     * Set productResortId.
     *
     * @param int|null $productResortId
     *
     * @return GoodsIssue
     */
    public function setProductResortId($productResortId = null)
    {
        $this->productResortId = $productResortId;

        return $this;
    }

    /**
     * Get productResortId.
     *
     * @return int|null
     */
    public function getProductResortId()
    {
        return $this->productResortId;
    }

    /**
     * Set shipmentIssueId.
     *
     * @param int|null $shipmentIssueId
     *
     * @return GoodsIssue
     */
    public function setShipmentIssueId($shipmentIssueId = null)
    {
        $this->shipmentIssueId = $shipmentIssueId;

        return $this;
    }

    /**
     * Get shipmentIssueId.
     *
     * @return int|null
     */
    public function getShipmentIssueId()
    {
        return $this->shipmentIssueId;
    }

    /**
     * Set isFullCompleteSet.
     *
     * @param bool|null $isFullCompleteSet
     *
     * @return GoodsIssue
     */
    public function setIsFullCompleteSet($isFullCompleteSet = null)
    {
        $this->isFullCompleteSet = $isFullCompleteSet;

        return $this;
    }

    /**
     * Get isFullCompleteSet.
     *
     * @return bool|null
     */
    public function getIsFullCompleteSet()
    {
        return $this->isFullCompleteSet;
    }

    /**
     * Set completeSetDetails.
     *
     * @param string|null $completeSetDetails
     *
     * @return GoodsIssue
     */
    public function setCompleteSetDetails($completeSetDetails = null)
    {
        $this->completeSetDetails = $completeSetDetails;

        return $this;
    }

    /**
     * Get completeSetDetails.
     *
     * @return string|null
     */
    public function getCompleteSetDetails()
    {
        return $this->completeSetDetails;
    }

    /**
     * Set isNewGoodsCondition.
     *
     * @param bool|null $isNewGoodsCondition
     *
     * @return GoodsIssue
     */
    public function setIsNewGoodsCondition($isNewGoodsCondition = null)
    {
        $this->isNewGoodsCondition = $isNewGoodsCondition;

        return $this;
    }

    /**
     * Get isNewGoodsCondition.
     *
     * @return bool|null
     */
    public function getIsNewGoodsCondition()
    {
        return $this->isNewGoodsCondition;
    }

    /**
     * Set goodsConditionDetails.
     *
     * @param string|null $goodsConditionDetails
     *
     * @return GoodsIssue
     */
    public function setGoodsConditionDetails($goodsConditionDetails = null)
    {
        $this->goodsConditionDetails = $goodsConditionDetails;

        return $this;
    }

    /**
     * Get goodsConditionDetails.
     *
     * @return string|null
     */
    public function getGoodsConditionDetails()
    {
        return $this->goodsConditionDetails;
    }

    /**
     * Set clientRequirement.
     *
     * @param string|null $clientRequirement
     *
     * @return GoodsIssue
     */
    public function setClientRequirement($clientRequirement = null)
    {
        $this->clientRequirement = $clientRequirement;

        return $this;
    }

    /**
     * Get clientRequirement.
     *
     * @return string|null
     */
    public function getClientRequirement()
    {
        return $this->clientRequirement;
    }

    /**
     * @return \DateTime|null
     */
    public function getAcceptedAt()
    {
        return $this->acceptedAt;
    }

    /**
     * @param \DateTime|null $acceptedAt
     */
    public function setAcceptedAt($acceptedAt)
    {
        $this->acceptedAt = $acceptedAt;
    }

    /**
     * @return int|null
     */
    public function getAcceptedBy()
    {
        return $this->acceptedBy;
    }

    /**
     * @param int|null $acceptedBy
     */
    public function setAcceptedBy($acceptedBy)
    {
        $this->acceptedBy = $acceptedBy;
    }

    /**
     * @return null|string
     */
    public function getClientDecisionComment()
    {
        return $this->clientDecisionComment;
    }

    /**
     * @param null|string $clientDecisionComment
     */
    public function setClientDecisionComment($clientDecisionComment)
    {
        $this->clientDecisionComment = $clientDecisionComment;
    }

    /**
     * @return null|string
     */
    public function getGoodsDecisionComment()
    {
        return $this->goodsDecisionComment;
    }

    /**
     * @param null|string $goodsDecisionComment
     */
    public function setGoodsDecisionComment($goodsDecisionComment)
    {
        $this->goodsDecisionComment = $goodsDecisionComment;
    }
}
