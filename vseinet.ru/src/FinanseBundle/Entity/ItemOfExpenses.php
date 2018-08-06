<?php

namespace FinanseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * статьи расходов/доходов
 *
 * @ORM\Table(name="item_of_expenses")
 * @ORM\Entity()
 */

class ItemOfExpenses
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
     * @ORM\Column(name="pid", type="integer", nullable=true)
     */
    private $pid;
    
    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    private $name;
    
    /**
     * @var string
     * @ORM\Column(name="search_tags", type="string", nullable=true)
     */
    private $searchTags;
    
    /**
     * @var boolean
     * @ORM\Column(name="is_group", type="boolean")
     */
    private $isGroup;
    
    /**
     * @var boolean
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * @var string
     * @ORM\Column(name="magic_mnemo", type="string", nullable=true)
     */
    private $magicMnemo;
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    ///////////////////////////
    //
    //  Методы
    //
    
    /**
     * Получить идентификатор
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Получить идентификатор родительской группы
     * @return int|null
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Установить идентификатор родительской группы
     * @param int|null $pid
     * @return ItemOfExpenses
     */
    public function setParentDocumentId($pid = null)
    {
        $this->pid = $pid;
        return $this;
    }

    /**
     * Получить наименование
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Установить наименование
     * @param string $name
     * @return ItemOfExpenses
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Получить дополнительные коды поиска
     * @return string|null
     */
    public function getSearchTags()
    {
        return $this->searchTags;
    }

    /**
     * Установить дополнительные коды поиска
     * @param string|null $searchTags
     * @return ItemOfExpenses
     */
    public function setSearchTags($searchTags = null)
    {
        $this->searchTags = $searchTags;
        return $this;
    }

    /**
     * Получить признак группы
     * @return boolean
     */
    public function getIsGroup()
    {
        return $this->isGroup;
    }

    /**
     * Установить признак группы
     * @param bool $isGroup
     * @return ItemOfExpenses
     */
    public function setIsGroup(bool $isGroup = False)
    {
        $this->isGroup = $isGroup;
        return $this;
    }

    /**
     * Получить признак активности
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Установить признак активности
     * @param boolean $isActive
     * @return ItemOfExpenses
     */
    public function setIsActive(bool $isActive = True)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * Получить магическую мнемонику статьи расхода
     * @return string|null
     */
    public function getMagicMnemo()
    {
        return $this->magicMnemo;
    }

    /**
     * Установить магическую мнемонику статьи расхода
     * @param string|null $magicMnemo
     * @return ItemOfExpenses
     */
    public function setMagicMnemo($magicMnemo = null)
    {
        $this->magicMnemo = $magicMnemo;
        return $this;
    }

    // </editor-fold>
    
}
