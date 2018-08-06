<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * GoodsAcceptanceItem
 *
 * @ORM\Table(name="goods_acceptance_item")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\GoodsAcceptanceItemRepository")
 */
class GoodsAcceptanceItem
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
     * @var integer
     * @ORM\Column(name="goods_acceptance_did", type="integer")
     */
    private $goodsAcceptanceDId;

    /**
     * @var integer
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;

    /**
     * @var int|null
     * @ORM\Column(name="goods_pallet_id", type="integer", nullable=true)
     */
    private $goodsPalletId;

    /**
     * @var integer
     * @ORM\Column(name="order_item_id", type="integer", nullable=true)
     */
    private $orderItemId;

    /**
     * @var integer
     * @ORM\Column(name="supply_item_id", type="integer", nullable=true)
     */
    private $supplyItemId;

    /**
     * @var integer
     * @ORM\Column(name="quantity", type="integer", options={"default": 0})
     */
    private $quantity;

    /**
     * @var integer
     * @ORM\Column(name="initial_quantity", type="integer")
     */
    private $initialQuantity;

    /**
     * @var string
     * @ORM\Column(name="goods_state_code", type="string", options={"default": "normal"})
     */
    private $goodsStateCode;
    
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
     * Get goodsAcceptanceId
     *
     * @return int
     */
    public function getGoodsAcceptanceDId()
    {
        return $this->goodsAcceptanceDId;
    }

    /**
     * Set goodsReleaseId
     *
     * @param int $goodsReleaseId
     *
     * @return GoodsAcceptanceItem
     */
    public function setGoodsAcceptanceDId($goodsAcceptanceId)
    {
        $this->goodsAcceptanceDId = $goodsAcceptanceId;

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
     * @return GoodsAcceptanceItem
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
     * @return GoodsAcceptanceItem
     */
    public function setGoodsPalletId($goodsPalletId = null)
    {
        $this->goodsPalletId = $goodsPalletId;

        return $this;
    }

    /**
     * Get orderItemId
     *
     * @return int|null
     */
    public function getOrderItemId()
    {
        return $this->orderItemId;
    }

    /**
     * Set orderItemId
     *
     * @param int|null $orderItemId
     *
     * @return GoodsAcceptanceItem
     */
    public function setOrderItemId($orderItemId = null)
    {
        $this->orderItemId = $orderItemId;

        return $this;
    }

    /**
     * Get supplyItemId
     *
     * @return int|null
     */
    public function getSupplyItemId()
    {
        return $this->supplyItemId;
    }

    /**
     * Set supplyItemId
     *
     * @param int|null $supplyItemId
     *
     * @return GoodsAcceptanceItem
     */
    public function setSupplyItemId($supplyItemId = null)
    {
        $this->supplyItemId = $supplyItemId;

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
     * @return GoodsAcceptanceItem
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
     * @return GoodsAcceptanceItem
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
     * @param string|null $goodsStateCode
     *
     * @return GoodsAcceptanceItem
     */
    public function setGoodsStateCode($goodsStateCode = 'normal')
    {

        $this->goodsStateCode = $goodsStateCode;

        return $this;
    }

    // </editor-fold>

    
    function __clone() {
        $this->id = NULL;
    }

}

