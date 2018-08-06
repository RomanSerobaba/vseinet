<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierReserveItem
 *
 * @ORM\Table(name="supplier_reserve_item")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\SupplierReserveItemRepository")
 */
class SupplierReserveItem
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
     * @ORM\Column(name="supplier_reserve_id", type="integer")
     */
    private $supplierReserveId;

    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;

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
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;


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
     * Set supplierReserveId.
     *
     * @param int $supplierReserveId
     *
     * @return SupplierReserveItem
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
     * Set baseProductId.
     *
     * @param int $baseProductId
     *
     * @return SupplierReserveItem
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
     * Set orderItemId.
     *
     * @param int|null $orderItemId
     *
     * @return SupplierReserveItem
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
     * @return SupplierReserveItem
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
     * Set quantity.
     *
     * @param int $quantity
     *
     * @return SupplierReserveItem
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
}
