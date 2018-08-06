<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierReserveChangeItem
 *
 * @ORM\Table(name="supplier_reserve_change_item")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\SupplierReserveChangeItemRepository")
 */
class SupplierReserveChangeItem
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
     * @ORM\Column(name="delta", type="integer")
     */
    private $delta;

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
     * @ORM\Column(name="supplier_reserve_change_id", type="integer")
     */
    private $supplierReserveChangeId;

    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;


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
     * Set delta.
     *
     * @param int $delta
     *
     * @return SupplierReserveChangeItem
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
     * Set orderItemId.
     *
     * @param int|null $orderItemId
     *
     * @return SupplierReserveChangeItem
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
     * @return SupplierReserveChangeItem
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
     * Set supplierReserveChangeId.
     *
     * @param int $supplierReserveChangeId
     *
     * @return SupplierReserveChangeItem
     */
    public function setSupplierReserveChangeId($supplierReserveChangeId)
    {
        $this->supplierReserveChangeId = $supplierReserveChangeId;

        return $this;
    }

    /**
     * Get supplierReserveChangeId.
     *
     * @return int
     */
    public function getSupplierReserveChangeId()
    {
        return $this->supplierReserveChangeId;
    }

    /**
     * Set baseProductId.
     *
     * @param int $baseProductId
     *
     * @return SupplierReserveChangeItem
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
}
