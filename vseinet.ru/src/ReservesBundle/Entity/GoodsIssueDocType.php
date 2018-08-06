<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Документ - Претензия - Шапка
 *
 * @ORM\Table(name="goods_issue_doc_type")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\GoodsIssueDocTypeRepository")
 */

class GoodsIssueDocType
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
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * @var bool
     * @ORM\Column(name="is_interactive", type="boolean")
     */
    private $isInteractive;
    
    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    private $name;
    
    /**
     * @var string
     * @ORM\Column(name="available_goods_states", type="string")
     */
    private $availableGoodsStates;
    
    /**
     * @var bool
     * @ORM\Column(name="by_goods", type="boolean")
     */
    private $byGoods;
    
    /**
     * @var bool
     * @ORM\Column(name="by_client", type="boolean")
     */
    private $byClient;
    
    /**
     * @var bool
     * @ORM\Column(name="by_supplier", type="boolean")
     */
    private $bySupplier;
    
    /**
     * @var bool
     * @ORM\Column(name="make_issue_reserve", type="boolean")
     */
    private $makeIssueReserve;
    
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
     * @param bool $isActive
     *
     * @return GoodsIssueDocType
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    // field interactive

    /**
     * Получить признак интерактивного использования
     *
     * @return bool
     */
    public function getIsInteractive()
    {
        return $this->isInteractive;
    }

    /**
     * Установить признак интерактивного использования
     *
     * @param bool $isInteractive
     *
     * @return GoodsIssueDocType
     */
    public function setIsInteractive($isInteractive)
    {
        $this->isInteractive = $isInteractive;

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
     * @return GoodsIssueDocType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    // field availableGoodsStates
    
    /**
     * Получение списка допустимых состояний товара
     *
     * @return array
     */
    public function getAvailableGoodsStates()
    {
        if (empty($this->availableGoodsStates)) {
            return [];
        }else{
            return split(',', str_replace(['{','}'], '', $this->availableGoodsStates));
        }

    }

    /**
     * Установка списка допустимых состояний товара
     *
     * @param array $availableGoodsStates
     *
     * @return GoodsIssueDocType
     */
    public function setAvailableGoodsStates($availableGoodsStates = [])
    {
        $this->availableGoodsStates = '{'. implode(',', $availableGoodsStates) .'}';
        return $this;
    }

    // field byGoods
    
    /**
     * Получить признак претензии - по продукту
     *
     * @return GoodsIssueDocType
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
     * @return GoodsIssueDocType
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
     * @return GoodsIssueDocType
     */
    public function setByClient($byClient)
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
     * @return GoodsIssueDocType
     */
    public function setBySupplier($bySupplier)
    {
        $this->bySupplier = $bySupplier;
        
        return $this;
    }

    // field makeIssueReserve
    
    /**
     * Получение признак претензии - по поставщику
     *
     * @return bool
     */
    public function getMakeIssueReserve()
    {
        return $this->makeIssueReserve;
    }

    /**
     * Установка признак претензии - по поставщику
     *
     * @param bool $makeIssueReserve
     *
     * @return GoodsIssueDocType
     */
    public function setMakeIssueReserve($makeIssueReserve = false)
    {
        $this->makeIssueReserve = $makeIssueReserve;
        
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
        $this->makeIssueReserve = false;
    }
    
    function __clone()
    {
        $this->id = null;
    }
    
    // </editor-fold>
    
}
