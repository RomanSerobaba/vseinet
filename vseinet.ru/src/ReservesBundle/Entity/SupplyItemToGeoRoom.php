<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplyItemToGeoRoom
 *
 * @ORM\Table(name="supply_item_to_geo_room")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\SupplyItemToGeoRoomRepository")
 */
class SupplyItemToGeoRoom
{
    const TYPE_SALE = 'sale';
    const TYPE_RESERVED = 'reserved';
    const TYPE_WRITTEN_OFF = 'written_off';
    const TYPE_ISSUED = 'issued';
    const TYPE_EQUIPMENT = 'equipment';

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
     * @ORM\Column(name="supply_item_id", type="integer")
     */
    private $supplyItemId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_room_id", type="integer")
     */
    private $geoRoomId;

    /**
     * @var string
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_in_transit", type="boolean")
     */
    private $isInTransit;

    /**
     * @var int
     *
     * @ORM\Column(name="order_item_id", type="integer", nullable=true)
     */
    private $orderItemId;

    /**
     * @var int
     *
     * @ORM\Column(name="product_issue_id", type="integer", nullable=true)
     */
    private $productIssueId;

    /**
     * @var int
     *
     * @ORM\Column(name="product_request_id", type="integer", nullable=true)
     */
    private $productRequestId;

    /**
     * @var int
     *
     * @ORM\Column(name="equipment_id", type="integer", nullable=true)
     */
    private $equipmentId;


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
     * Set supplyItemId
     *
     * @param integer $supplyItemId
     *
     * @return SupplyItemToGeoRoom
     */
    public function setSupplyItemId($supplyItemId)
    {
        $this->supplyItemId = $supplyItemId;

        return $this;
    }

    /**
     * Get supplyItemId
     *
     * @return int
     */
    public function getSupplyItemId()
    {
        return $this->supplyItemId;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return SupplyItemToGeoRoom
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
     * Set geoRoomId
     *
     * @param integer $geoRoomId
     *
     * @return SupplyItemToGeoRoom
     */
    public function setGeoRoomId($geoRoomId)
    {
        $this->geoRoomId = $geoRoomId;

        return $this;
    }

    /**
     * Get geoRoomId
     *
     * @return int
     */
    public function getGeoRoomId()
    {
        return $this->geoRoomId;
    }

    /**
     * Set quantity
     *
     * @param string $quantity
     *
     * @return SupplyItemToGeoRoom
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set isInTransit
     *
     * @param boolean $isInTransit
     *
     * @return SupplyItemToGeoRoom
     */
    public function setIsInTransit($isInTransit)
    {
        $this->isInTransit = $isInTransit;

        return $this;
    }

    /**
     * Get isInTransit
     *
     * @return bool
     */
    public function getIsInTransit()
    {
        return $this->isInTransit;
    }

    /**
     * Set orderItemId
     *
     * @param integer $orderItemId
     *
     * @return SupplyItemToGeoRoom
     */
    public function setOrderItemId($orderItemId)
    {
        $this->orderItemId = $orderItemId;

        return $this;
    }

    /**
     * Get orderItemId
     *
     * @return int
     */
    public function getOrderItemId()
    {
        return $this->orderItemId;
    }

    /**
     * Set productIssueId
     *
     * @param integer $productIssueId
     *
     * @return SupplyItemToGeoRoom
     */
    public function setProductIssueId($productIssueId)
    {
        $this->productIssueId = $productIssueId;

        return $this;
    }

    /**
     * Get productIssueId
     *
     * @return int
     */
    public function getProductIssueId()
    {
        return $this->productIssueId;
    }

    /**
     * Set productRequestId
     *
     * @param integer $productRequestId
     *
     * @return SupplyItemToGeoRoom
     */
    public function setProductRequestId($productRequestId)
    {
        $this->productRequestId = $productRequestId;

        return $this;
    }

    /**
     * Get productRequestId
     *
     * @return int
     */
    public function getProductRequestId()
    {
        return $this->productRequestId;
    }

    /**
     * Set equipmentId
     *
     * @param integer $equipmentId
     *
     * @return SupplyItemToGeoRoom
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
}

