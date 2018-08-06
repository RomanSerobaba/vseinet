<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Документ - Решение по претензии - Шапка
 *
 * @ORM\Table(name="goods_decision_doc")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\GoodsDecisionDocRepository")
 */

class GoodsDecisionDoc
{
    use \DocumentBundle\Prototipe\DocumentEntity;
    
    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    ///////////////////////////
    //
    //  Поля
    //
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_room_id", type="integer", nullable=true)
     */
    private $geoRoomId;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="goods_issue_doc_id", type="integer")
     */
    private $goodsIssueDocumentId;

    /**
     * @var int
     *
     * @ORM\Column(name="goods_decision_doc_type_id", type="integer")
     */
    private $goodsDecisionDocTypeId;
    
    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer", nullable=true)
     */
    private $baseProductId;
    
    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer", nullable=true)
     */
    private $price;
    
    /**
     * @var int
     *
     * @ORM\Column(name="money_back", type="integer", nullable=true)
     */
    private $moneyBack;
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    ///////////////////////////
    //
    //  Методы
    //
    
    // field description

    /**
     * Получить описание решения претензии
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Установить описание решения претензии
     *
     * @param string $description
     *
     * @return GoodsDecisionDoc
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    // field quantity

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
     * @return GoodsDecisionDoc
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    // field goodsIssueDocId

    /**
     * Получить идентификатор претензии
     *
     * @return int
     */
    public function getGoodsIssueDocumentId()
    {
        return $this->goodsIssueDocumentId;
    }

    /**
     * Установить идентификатор претензии
     *
     * @param int $goodsIssueDocumnetId Уникальный идентификатор документа-претензии
     *
     * @return GoodsDecisionDoc
     */
    public function setGoodsIssueDocumentId($goodsIssueDocumnetId)
    {
        $this->goodsIssueDocumentId = $goodsIssueDocumnetId;

        return $this;
    }

    // field geoRoomId

    /**
     * Получить идентификатор склада
     *
     * @return int
     */
    public function getGeoRoomId()
    {
        return $this->geoRoomId;
    }

    /**
     * Установить идентификатор склада
     *
     * @param int $geoRoomId
     *
     * @return GoodsDecisionDoc
     */
    public function setGeoRoomId($geoRoomId)
    {
        $this->geoRoomId = $geoRoomId;

        return $this;
    }

    // field goodsDecisionDocTypeId

    /**
     * Получить тип документа
     *
     * @return int
     */
    public function getGoodsDecisionDocTypeId()
    {
        return $this->goodsDecisionDocTypeId;
    }

    /**
     * Установить тип документа
     *
     * @param int
     *
     * @return GoodsDecisionDoc
     */
    public function setGoodsDecisionDocTypeId($goodsDecisionDocTypeId)
    {
        $this->goodsDecisionDocTypeId = $goodsDecisionDocTypeId;

        return $this;
    }

    // field baseProductId

    /**
     * Получить идентификатор склада
     *
     * @return int
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Установить идентификатор склада
     *
     * @param int $baseProductId
     *
     * @return GoodsDecisionDoc
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    // field price

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price
     * @return GoodsDecisionDoc
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    // field moneyBack

    /**
     * @return int
     */
    public function getMoneyBack()
    {
        return $this->moneyBack;
    }

    /**
     * @param int $moneyBack
     * @return GoodsDecisionDoc
     */
    public function setMoneyBack($moneyBack)
    {
        $this->moneyBack = $moneyBack;

        return $this;
    }

    // </editor-fold>
    
}
