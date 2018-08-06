<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GoodsReleaseDocItem
 *
 * @ORM\Table(name="goods_release_item")
 * @ORM\Entity()
 */
class GoodsReleaseDocItem
{

    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int|null
     * @ORM\Column(name="goods_pallet_id", type="integer")
     */
    private $goodsPalletId;

    /**
     * @var integer
     *
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;

    /**
     * @var integer
     * @ORM\Column(name="quantity", type="integer", options={"default": 0})
     */
    private $quantity;

    /**
     * @var integer
     * @ORM\Column(name="order_item_id", type="integer")
     */
    private $orderItemId;

    /**
     * @var integer
     * @ORM\Column(name="initial_quantity", type="integer")
     */
    private $initialQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="goods_release_did", type="integer")
     */
    private $goodsReleaseDId;

    /**
     * @var string
     * @ORM\Column(name="goods_state_code", type="string", options={"default": "normal"})
     */
    private $goodsStateCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="supply_item_id", type="integer")
     */
    private $supplyItemId;
    
    // </editor-fold>
    
    
    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    /**
     * Get Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    
    /**
     * Get goodsReleaseId
     *
     * @return int
     */
    public function getGoodsReleaseId()
    {
        return $this->goodsReleaseDId;
    }

    /**
     * Set goodsReleaseId
     *
     * @param int $goodsReleaseId
     *
     * @return GoodsReleaseDocItem
     */
    public function setGoodsReleaseId($goodsReleaseId)
    {
        $this->goodsReleaseDId = $goodsReleaseId;

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
     * Set baseProductId
     *
     * @param int $baseProductId
     *
     * @return GoodsReleaseDocItem
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Получить иденификатор паллеты
     *
     * @return int|null
     */
    public function getGoodsPalletId()
    {
        return $this->goodsPalletId;
    }

    /**
     * Установить идентификатор паллеты
     *
     * @param int|null $goodsPalletId
     *
     * @return GoodsReleaseDocItem
     */
    public function setGoodsPalletId($goodsPalletId = null)
    {
        $this->goodsPalletId = $goodsPalletId;

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
     * Set orderItemId
     *
     * @param int $orderItemId
     *
     * @return GoodsReleaseDocItem
     */
    public function setOrderItemId($orderItemId)
    {
        $this->orderItemId = $orderItemId;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return GoodsReleaseDocItem
     */
    public function setQuantity($quantity = 0)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get initial quantity
     *
     * @return integer
     */
    public function getInitialQuantity()
    {
        return $this->initialQuantity;
    }

    /**
     * Set init quantity
     *
     * @param integer $initialQuantity
     *
     * @return GoodsReleaseDocItem
     */
    public function setInitialQuantity($initialQuantity)
    {
        $this->initialQuantity = $initialQuantity;

        return $this;
    }

    /**
     * Get defect type
     *
     * @return string
     */
    public function getGoodsStateCode()
    {
        return $this->goodsStateCode;
    }

    /**
     * Set defect type
     *
     * @param string|null $defectType
     *
     * @return GoodsReleaseDocItem
     */
    public function setGoodsStateCode($defectType = 'normal')
    {

        $this->goodsStateCode = $defectType;

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
     * Set supplyItemId
     *
     * @param int $supplyItemId
     *
     * @return GoodsReleaseDocItem
     */
    public function setSupplyItemId($supplyItemId)
    {
        $this->supplyItemId = $supplyItemId;

        return $this;
    }

    // </editor-fold>

    
    function __clone() {
        $this->id = NULL;
    }
}

