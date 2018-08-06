<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Документ - Претензия - Шапка
 *
 * @ORM\Table(name="goods_decision_doc_type")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\GoodsDecisionDocTypeRepository")
 */

class GoodsDecisionDocType
{
    
    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    ///////////////////////////
    //
    //  Поля
    //
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="need_geo_room_id", type="boolean")
     */
    private $needGeoRoomId;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="by_goods", type="boolean")
     */
    private $byGoods;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="by_client", type="boolean")
     */
    private $byClient;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="by_supplier", type="boolean")
     */
    private $bySupplier;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="need_base_product_id", type="boolean")
     */
    private $needBaseProductId;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="need_price", type="boolean")
     */
    private $needPrice;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="need_money_back", type="boolean")
     */
    private $needMoneyBack;
    
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

    // field active

    /**
     * Получить признак использования типа претензии
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Установить признак использования типа претензии
     *
     * @param bool $active
     *
     * @return GoodsDecisionDocType
     */
    public function setIsActive($active)
    {
        $this->isActive = $active;

        return $this;
    }

    // field goodsIssueDocTypeId

    /**
     * Получить тип претензии, для которого актуален тип данного решения
     *
     * @return int
     */
    public function getGoodsIssueDocTypeid()
    {
        return $this->goodsIssueDocTypeid;
    }

    /**
     * Установить тип претензии, для которого актуален тип данного решения
     *
     * @param int $goodsIssueDocTypeid
     *
     * @return GoodsDecisionDocType
     */
    public function setGoodsIssueDocTypeid($goodsIssueDocTypeid)
    {
        $this->goodsIssueDocTypeid = $goodsIssueDocTypeid;

        return $this;
    }

    // field name

    /**
     * Получить наименование типа претензии
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Установить наиментвание типа претензии
     *
     * @param string $name
     *
     * @return GoodsDecisionDocType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    // field needGeoRoomId
    
    /**
     * Получить признак обязательности указания склада
     *
     * @return GoodsDecisionDocType
     */
    public function getNeedGeoRoomId()
    {
        return $this->needGeoRoomId;
    }

    /**
     * Установить признак обязательности указания склада
     *
     * @param bool $needGeoRoomId
     *
     * @return GoodsDecisionDocType
     */
    public function setNeedGeoRoomId($needGeoRoomId)
    {
        $this->needGeoRoomId = $needGeoRoomId;

        return $this;
    }
    
    // field byGoods
    
    /**
     * Получить признак претензии - по продукту
     *
     * @return GoodsDecisionDocType
     */
    public function getByGoods()
    {
        return $this->byGoods;
    }

    /**
     * Установить признак претензии - по продукту
     *
     * @param bool $byGoods
     *
     * @return GoodsDecisionDocType
     */
    public function setByGoods($byGoods)
    {
        $this->byGoods = $byGoods;

        return $this;
    }
    
    // field byClient
    
    /**
     * Получить признак претензии - по клиенту
     *
     * @return bool
     */
    public function getByClient()
    {
        return $this->byClient;
    }

    /**
     * Установить признак претензии - по клиенту
     *
     * @param bool $byClient
     *
     * @return GoodsDecisionDocType
     */
    public function setByClient($byClient = null)
    {
        $this->byClient = $byClient;

        return $this;
    }

    // field completedAt
    
    /**
     * Получение признак претензии - по поставщику
     *
     * @return bool
     */
    public function getBySupplier()
    {
        return $this->bySupplier;
    }

    /**
     * Установка признак претензии - по поставщику
     *
     * @param bool $bySupplier
     *
     * @return GoodsDecisionDocType
     */
    public function setBySupplier($bySupplier)
    {
        $this->bySupplier = $bySupplier;
        
        return $this;
    }

    // field needChBaseProductId
    
    /**
     * Получить признак необходимости указания идентификатора замены товара
     *
     * @return bool
     */
    public function getNeedBaseProductId()
    {
        return $this->needBaseProductId;
    }

    /**
     * Установить признак необходимости указания идентификатора замены товара
     *
     * @param bool $needBaseProductId
     *
     * @return GoodsDecisionDocType
     */
    public function setNeedBaseProductId($needBaseProductId)
    {
        $this->needBaseProductId = $needBaseProductId;
        
        return $this;
    }

    // field needPrice
    
    /**
     * Получить признак необходимости указания новой цены товара
     *
     * @return bool
     */
    public function getNeedPrice()
    {
        return $this->needPrice;
    }

    /**
     * Установить признак необходимости указания новой цены товара
     *
     * @param bool $needPrice
     *
     * @return GoodsDecisionDocType
     */
    public function setNeedPrice($needPrice)
    {
        $this->needPrice = $needPrice;
        
        return $this;
    }


    // field needMoneyBack
    
    /**
     * Получить признак необходимости указания новой цены товара
     *
     * @return bool
     */
    public function getNeedMoneyBack()
    {
        return $this->needMoneyBack;
    }

    /**
     * Установить признак необходимости указания новой цены товара
     *
     * @param bool $needMoneyBack
     *
     * @return GoodsDecisionDocType
     */
    public function setNeedMoneyBack($needMoneyBack)
    {
        $this->needMoneyBack = $needMoneyBack;
        
        return $this;
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Переопределение магических методов">
    
    ///////////////////////////
    //
    //  Переопределение магических методов
    //
    
    function __construct()
    {
        $this->isActive = true;
        $this->byGoods = false;
        $this->byClient = false;
        $this->bySupplier = false;
    }
    
    function __clone()
    {
        $this->id = null;
    }
    
    // </editor-fold>
    
}
