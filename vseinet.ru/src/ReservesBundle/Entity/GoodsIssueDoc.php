<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Документ - Претензия - Шапка
 *
 * @ORM\Table(name="goods_issue_doc")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\GoodsIssueDocRepository")
 */

class GoodsIssueDoc
{
    use \DocumentBundle\Prototipe\DocumentEntity;
    
    const STATUS_NEW = 'new';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    
    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    ///////////////////////////
    //
    //  Поля
    //
    
    /**
     * @var \DateTime
     * @ORM\Column(name="activated_at", type="datetime", nullable=true)
     */
    private $activatedAt;
    
    /**
     * @var string
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;
    
    /**
     * @var string
     * @ORM\Column(name="product_condition", type="string")
     */
    private $goodsCondition;
    
    /**
     * @var int
     * @ORM\Column(name="geo_room_id", type="integer", nullable=true)
     */
    private $geoRoomId;
    
    /**
     * @var int
     * @ORM\Column(name="supplier_id", type="integer", nullable=true)
     */
    private $supplierId;
    
    /**
     * @var int
     * @ORM\Column(name="goods_issue_doc_type_id", type="integer")
     */
    private $goodsIssueDocTypeId;
    
    /**
     * @var int
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;
    
    /**
     * @var int
     * @ORM\Column(name="order_item_id", type="integer", nullable=true)
     */
    private $orderItemId;
    
    /**
     * @var int
     * @ORM\Column(name="supply_item_id", type="integer", nullable=true)
     */
    private $supplyItemId;
    
    /**
     * @var int
     * @ORM\Column(name="goods_state_code", type="string")
     */
    private $goodsStateCode;
    
    /**
     * @var int
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;
    
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    ///////////////////////////
    //
    //  Методы
    //
    
    // field activatedAt
    
    /**
     * Получить дату активации претензии
     * @return \DateTime|null
     */
    public function getActivatedAt()
    {
        return $this->activatedAt;
    }

    /**
     * Установить дату создания документа
     * @param \DateTime|null $activatedAt
     * @return GoodsIssueDoc
     */
    public function setActivatedAt($activatedAt = null)
    {
        $this->activatedAt = $activatedAt;

        return $this;
    }
    //field description;
    
    /**
     * Получить описание претензии
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Установить описание претензии
     *
     * @param string|null $description
     *
     * @return GoodsIssueDoc
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }
    
    //field productCondition;
    
    /**
     * Получить описание состояния товара
     * @return string|null
     */
    public function getGoodsCondition()
    {
        return $this->goodsCondition;
    }

    /**
     * Установить описание состояния товара
     * @param string|null $goodsCondition
     * @return GoodsIssueDoc
     */
    public function setGoodsCondition($goodsCondition = null)
    {
        $this->goodsCondition = $goodsCondition;

        return $this;
    }
    
    //field geoRoomId;
    
    /**
     * Получить идентификатор склада товара с претензией
     *
     * @return int|null
     */
    public function getGeoRoomId()
    {
        return $this->geoRoomId;
    }

    /**
     * Установить идентификатор склада товара с претензией
     *
     * @param int|null $geoRoomId
     *
     * @return GoodsIssueDoc
     */
    public function setGeoRoomId($geoRoomId)
    {
        $this->geoRoomId = $geoRoomId;

        return $this;
    }
    
    //field supplierId;
    
    /**
     * Получить идентификатор поставщика товара с претензией
     *
     * @return int|null
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * Установить идентификатор поставщика товара с претензией
     *
     * @param int|null $supplierId
     *
     * @return GoodsIssueDoc
     */
    public function setSupplierId($supplierId)
    {
        $this->supplierId = $supplierId;

        return $this;
    }
    
    //field typeCode;
    
    /**
     * Получить тип операции документа
     *
     * @return int
     */
    public function getGoodsIssueDocTypeId()
    {
        return $this->goodsIssueDocTypeId;
    }

    /**
     * Установить тип операции документа
     *
     * @param int $goodsIssueDocTypeId
     *
     * @return GoodsIssueDoc
     */
    public function setGoodsIssueDocTypeId($goodsIssueDocTypeId)
    {
        $this->goodsIssueDocTypeId = $goodsIssueDocTypeId;

        return $this;
    }

    //field baseProductId;
    
    /**
     * Получить идентификатор товара
     *
     * @return int
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Установить идентификатор товара
     *
     * @param int $baseProductId
     *
     * @return GoodsIssueDoc
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    //field orderItemId;
    
    /**
     * Получить идентификатор заказа клента
     *
     * @return int|null
     */
    public function getOrderItemId()
    {
        return $this->orderItemId;
    }

    /**
     * Установить идентификатор заказа клиента
     *
     * @param int|null $orderItemId
     *
     * @return GoodsIssueDoc
     */
    public function setOrderItemId($orderItemId = null)
    {
        $this->orderItemId = $orderItemId;

        return $this;
    }

    //field supplyItemId;
    
    /**
     * Получить идентификатор заказа поставщику
     *
     * @return int|null
     */
    public function getSupplyItemId()
    {
        return $this->supplyItemId;
    }

    /**
     * Установить идентификатор заказа поставщику
     *
     * @param int|null $supplyItemId
     *
     * @return GoodsIssueDoc
     */
    public function setSupplyItemId($supplyItemId = null)
    {
        $this->supplyItemId = $supplyItemId;

        return $this;
    }
    
    //field goodsStateCode;
    
    /**
     * Получить код качества товара
     *
     * @return string
     */
    public function getGoodsStateCode()
    {
        return $this->goodsStateCode;
    }

    /**
     * Установить код качества товара
     *
     * @param string $goodsStateCode
     *
     * @return GoodsIssueDoc
     */
    public function setGoodsStateCode($goodsStateCode)
    {
        $this->goodsStateCode = $goodsStateCode;

        return $this;
    }
    
    //field goodsStateCode;
    
    /**
     * Получить количество
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Установить количество
     *
     * @param int $quantity
     *
     * @return GoodsIssueDoc
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }
    
    // </editor-fold>
    
}
