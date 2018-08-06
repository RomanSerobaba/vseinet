<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GoodsReserveRegisterCurrent
 *
 * @ORM\Table(name="goods_reserve_register_current")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\GoodsReserveRegisterCurrentRepository")
 */
class GoodsReserveRegisterCurrent
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
     * @var string
     *
     * @ORM\Column(name="goods_condition_code", type="string", length=255)
     */
    private $goodsConditionCode;

    /**
     * @var int
     *
     * @ORM\Column(name="supply_item_id", type="integer")
     */
    private $supplyItemId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="order_item_id", type="integer", nullable=true)
     */
    private $orderItemId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="geo_room_id", type="integer", nullable=true)
     */
    private $geoRoomId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="goods_release_id", type="integer", nullable=true)
     */
    private $goodsReleaseId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="goods_pallet_id", type="integer", nullable=true)
     */
    private $goodsPalletId;

    /**
     * @var int
     *
     * @ORM\Column(name="delta", type="integer")
     */
    private $delta;


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
     * @return GoodsReserveRegisterCurrent
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
     * Set goodsConditionCode.
     *
     * @param string $goodsConditionCode
     *
     * @return GoodsReserveRegisterCurrent
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
     * Set supplyItemId.
     *
     * @param int $supplyItemId
     *
     * @return GoodsReserveRegisterCurrent
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
     * Set orderItemId.
     *
     * @param int|null $orderItemId
     *
     * @return GoodsReserveRegisterCurrent
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
     * Set geoRoomId.
     *
     * @param int|null $geoRoomId
     *
     * @return GoodsReserveRegisterCurrent
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
     * Set goodsReleaseId.
     *
     * @param int|null $goodsReleaseId
     *
     * @return GoodsReserveRegisterCurrent
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
     * Set goodsPalletId.
     *
     * @param int|null $goodsPalletId
     *
     * @return GoodsReserveRegisterCurrent
     */
    public function setGoodsPalletId($goodsPalletId = null)
    {
        $this->goodsPalletId = $goodsPalletId;

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

    /**
     * Set delta.
     *
     * @param int $delta
     *
     * @return GoodsReserveRegisterCurrent
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
}
