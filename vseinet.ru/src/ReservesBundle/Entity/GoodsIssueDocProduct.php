<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Документ - Претензия - Шапка
 *
 * @ORM\Table(name="goods_issue_doc_product")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\GoodsIssueDocProductRepository")
 */

class GoodsIssueDocProduct
{
    
    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    ///////////////////////////
    //
    //  Поля
    //
    
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="goods_issue_doc_did", type="integer")
     */
    private $goodsIssueDocId;

    /**
     * @var int
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;
    
    /**
     * @var string
     * @ORM\Column(name="goods_state_code", type="string")
     */
    private $goodsStateCode;
    
    /**
     * @var int
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;
    
    /**
     * @var int
     *
     * @ORM\Column(name="order_item_id", type="integer")
     */
    private $orderItemId;
    
    /**
     * @var int
     *
     * @ORM\Column(name="supply_item_id", type="integer")
     */
    private $supplyItemId;
    
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    ///////////////////////////
    //
    //  Методы
    //
    
    // field id 

    /**
     * Получить идентификатор
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    // field goodsIssueDocId

    /**
     * Получить признак использования типа претензии
     *
     * @return int
     */
    public function getGoodsIssueDocId()
    {
        return $this->goodsIssueDocId;
    }

    /**
     * Установить признак использования типа претензии
     *
     * @param int $goodsIssueDocId
     * @return GoodsIssueDocProduct
     */
    public function setGoodsIssueDocId($goodsIssueDocId)
    {
        $this->goodsIssueDocId = $goodsIssueDocId;

        return $this;
    }

    // field quantity

    /**
     * Получить количество
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Установить количество
     * @param int $quantity
     * @return GoodsIssueDocProduct
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    // field baseProductId

    /**
     * Получить идентификатор товара
     * @return int
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Установить идентификатор товара
     * @param int $baseProductId
     * @return GoodsIssueDocProduct
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    // field orderItemId

    /**
     * Получить идентификатор элемента заказа
     * @return int
     */
    public function getOrderItemId()
    {
        return $this->orderItemId;
    }

    /**
     * Установить идентификатор элемента заказа
     * @param int $orderItemId
     * @return GoodsIssueDocProduct
     */
    public function setOrderItemId($orderItemId)
    {
        $this->orderItemId = $orderItemId;

        return $this;
    }

    // field supplyItemId

    /**
     * Получить идентификатор элемента партии
     * @return int
     */
    public function getSupplyItemId()
    {
        return $this->supplyItemId;
    }

    /**
     * Установить идентификатор элемента партии
     * @param int $supplyItemId
     * @return GoodsIssueDocProduct
     */
    public function setSupplyItemId($supplyItemId)
    {
        $this->supplyItemId = $supplyItemId;

        return $this;
    }

    // field goodsStateCode

    /**
     * Получить идентификатор элемента партии
     * @return string
     */
    public function getGoodsStateCode()
    {
        return $this->goodsStateCode;
    }

    /**
     * Установить идентификатор элемента партии
     * @param string $goodsStateCode
     * @return GoodsIssueDocProduct
     */
    public function setGoodsStateCode($goodsStateCode)
    {
        $this->goodsStateCode = $goodsStateCode;

        return $this;
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Переопределение магических методов">
    
    ///////////////////////////
    //
    //  Переопределение магических методов
    //
    
    function __clone()
    {
        $this->id = null;
    }
    
    // </editor-fold>
    
}
