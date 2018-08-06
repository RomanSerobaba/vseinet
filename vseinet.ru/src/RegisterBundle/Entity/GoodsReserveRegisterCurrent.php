<?php

namespace RegisterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GoodsReserveRegisterCurrent
 *
 * @ORM\Table(name="goods_reserve_register_current")
 * @ORM\Entity(repositoryClass="RegisterBundle\Repository\GoodsReserveRegisterCurrentRepository")
 */
class GoodsReserveRegisterCurrent
{
    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $baseProductId;

    /**
     * @var string
     *
     * @ORM\Column(name="goods_condition_code", type="string")
     */
    private $goodsConditionCode;

    /**
     * @var int
     *
     * @ORM\Column(name="supply_item_id", type="integer")
     */
    private $supplyItemId;

    /**
     * @var int
     *
     * @ORM\Column(name="order_item_id", type="integer")
     */
    private $orderItemId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_room_id", type="integer")
     */
    private $geoRoomId;

    /**
     * @var int
     *
     * @ORM\Column(name="goods_release_id", type="integer")
     */
    private $goodsReleaseId;

    /**
     * @var int
     *
     * @ORM\Column(name="delta", type="integer")
     */
    private $delta;

    /**
     * @var int
     *
     * @ORM\Column(name="goods_pallet_id", type="integer")
     */
    private $goodsPalletId;


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
     * @param int $orderItemId
     *
     * @return GoodsReserveRegisterCurrent
     */
    public function setOrderItemId($orderItemId)
    {
        $this->orderItemId = $orderItemId;

        return $this;
    }

    /**
     * Get orderItemId.
     *
     * @return int
     */
    public function getOrderItemId()
    {
        return $this->orderItemId;
    }

    /**
     * Set geoRoomId.
     *
     * @param int $geoRoomId
     *
     * @return GoodsReserveRegisterCurrent
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
     * Set goodsReleaseId.
     *
     * @param int $goodsReleaseId
     *
     * @return GoodsReserveRegisterCurrent
     */
    public function setGoodsReleaseId($goodsReleaseId)
    {
        $this->goodsReleaseId = $goodsReleaseId;

        return $this;
    }

    /**
     * Get goodsReleaseId.
     *
     * @return int
     */
    public function getGoodsReleaseId()
    {
        return $this->goodsReleaseId;
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

    /**
     * Set goodsPalletId.
     *
     * @param int $goodsPalletId
     *
     * @return GoodsReserveRegisterCurrent
     */
    public function setGoodsPalletId($goodsPalletId)
    {
        $this->goodsPalletId = $goodsPalletId;

        return $this;
    }

    /**
     * Get goodsPalletId.
     *
     * @return int
     */
    public function getGoodsPalletId()
    {
        return $this->goodsPalletId;
    }
}
