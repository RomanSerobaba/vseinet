<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AvailableGoodsReservationItem
 *
 * @ORM\Table(name="available_goods_reservation_item")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\AvailableGoodsReservationItemRepository")
 */
class AvailableGoodsReservationItem
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
     * @ORM\Column(name="available_goods_reservation_id", type="integer")
     */
    private $availableGoodsReservationId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="order_item_id", type="integer", nullable=true)
     */
    private $orderItemId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="goods_request_id", type="integer", nullable=true)
     */
    private $goodsRequestId;

    /**
     * @var int
     *
     * @ORM\Column(name="delta", type="integer")
     */
    private $delta;

    /**
     * @var int|null
     *
     * @ORM\Column(name="geo_room_id", type="integer", nullable=true)
     */
    private $geoRoomId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="supplier_invoice_item_id", type="integer", nullable=true)
     */
    private $supplierInvoiceItemId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="goods_release_id", type="integer", nullable=true)
     */
    private $goodsReleaseId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="supply_item_id", type="integer", nullable=true)
     */
    private $supplyItemId;


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
     * Set availableGoodsReservationId.
     *
     * @param int $availableGoodsReservationId
     *
     * @return AvailableGoodsReservationItem
     */
    public function setAvailableGoodsReservationId($availableGoodsReservationId)
    {
        $this->availableGoodsReservationId = $availableGoodsReservationId;

        return $this;
    }

    /**
     * Get availableGoodsReservationId.
     *
     * @return int
     */
    public function getAvailableGoodsReservationId()
    {
        return $this->availableGoodsReservationId;
    }

    /**
     * Set orderItemId.
     *
     * @param int|null $orderItemId
     *
     * @return AvailableGoodsReservationItem
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
     * Set goodsRequestId.
     *
     * @param int|null $goodsRequestId
     *
     * @return AvailableGoodsReservationItem
     */
    public function setGoodsRequestId($goodsRequestId = null)
    {
        $this->goodsRequestId = $goodsRequestId;

        return $this;
    }

    /**
     * Get goodsRequestId.
     *
     * @return int|null
     */
    public function getGoodsRequestId()
    {
        return $this->goodsRequestId;
    }

    /**
     * Set delta.
     *
     * @param int $delta
     *
     * @return AvailableGoodsReservationItem
     */
    public function setDelta($delta)
    {
        $this->delta = $delta;

        return $this;
    }

    /**
     * Get delta.
     *
     * @return int
     */
    public function getDelta()
    {
        return $this->delta;
    }

    /**
     * Set geoRoomId.
     *
     * @param int|null $geoRoomId
     *
     * @return AvailableGoodsReservationItem
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
     * Set supplierInvoiceItemId.
     *
     * @param int|null $supplierInvoiceItemId
     *
     * @return AvailableGoodsReservationItem
     */
    public function setSupplierInvoiceItemId($supplierInvoiceItemId = null)
    {
        $this->supplierInvoiceItemId = $supplierInvoiceItemId;

        return $this;
    }

    /**
     * Get supplierInvoiceItemId.
     *
     * @return int|null
     */
    public function getSupplierInvoiceItemId()
    {
        return $this->supplierInvoiceItemId;
    }

    /**
     * Set goodsReleaseId.
     *
     * @param int|null $goodsReleaseId
     *
     * @return AvailableGoodsReservationItem
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
     * Set supplyItemId.
     *
     * @param int|null $supplyItemId
     *
     * @return AvailableGoodsReservationItem
     */
    public function setSupplyItemId($supplyItemId = null)
    {
        $this->supplyItemId = $supplyItemId;

        return $this;
    }

    /**
     * Get supplyItemId.
     *
     * @return int|null
     */
    public function getSupplyItemId()
    {
        return $this->supplyItemId;
    }
}
