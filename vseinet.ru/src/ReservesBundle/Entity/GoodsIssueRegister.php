<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Регистр претензий
 *
 * @ORM\Table(name="goods_issue_register")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\GoodsIssueRegisterRepository")
 */

class GoodsIssueRegister
{
    
    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    ///////////////////////////
    //
    //  Поля
    //
    
    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @var integer
     * @ORM\Column(name="created_by", type="integer")
     */
    private $createdBy;
    
    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="registrator_did", type="integer")
     */
    private $registratorDId;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="registered_at", type="datetime")
     */
    private $registeredAt;
    
    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="goods_issue_doc_did", type="integer")
     */
    private $goodsIssueDocDId;

    /**
     * @var integer
     * @ORM\Column(name="delta_goods", type="integer", nullable=true)
     */
    private $deltaGoods;

    /**
     * @var integer
     * @ORM\Column(name="delta_client", type="integer", nullable=true)
     */
    private $deltaClient;

    /**
     * @var integer
     *
     * @ORM\Column(name="delta_supplier", type="integer", nullable=true)
     */
    private $deltaSupplier;

    
    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    ///////////////////////////
    //
    //  Методы
    //
    
    // field createdAt
    
    /**
     * Получить дату создания докуменнта
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Установить дату создания документа
     *
     * @param \DateTime $createdAt
     *
     * @return GoodsIssueRegister
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    
    // field createdBy
    
    /**
     * Получить идентификатор автора документа
     *
     * @return int|null
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Установить идентификатор автора докмуента
     *
     * @param int|null $createdBy
     *
     * @return GoodsIssueRegister
     */
    public function setCreatedBy($createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    // field registratorDId

    /**
     * Получить идентификатор документа-регистратора
     *
     * @return int
     */
    public function getRegistratorDId()
    {
        return $this->registratorDId;
    }

    /**
     * Установить идентификатор документа-регистратора
     *
     * @param int $registratorDId
     *
     * @return GoodsIssueRegister
     */
    public function setRegistratorDId($registratorDId)
    {
        $this->registratorDId = $registratorDId;

        return $this;
    }

    // field registredAt
    
    /**
     * Получение даты актуальности записи
     *
     * @return \DateTime
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    /**
     * Установка даты актуальности записи
     *
     * @param \DateTime $registeredAt
     *
     * @return GoodsIssueRegister
     */
    public function setRegisteredAt($registeredAt)
    {
        $this->registeredAt = $registeredAt;
        
        return $this;
    }
    
    // field goodsIssueDocDId
    
    /**
     * Получить идентификатор претензии
     *
     * @return int
     */
    public function getGoodsIssueDocDId()
    {
        return $this->goodsIssueDocDId;
    }

    /**
     * Установить идентификатор претензии
     *
     * @param int $goodsIssueDocDId
     *
     * @return GoodsIssueRegister
     */
    public function setGoodsIssueDocDId($goodsIssueDocDId)
    {
        $this->goodsIssueDocDId = $goodsIssueDocDId;

        return $this;
    }
    
    // field deltaGoods
    
    /**
     * Получить количество не решенного по товару
     *
     * @return int|null
     */
    public function getDeltaGoods()
    {
        return $this->deltaGoods;
    }

    /**
     * Установить количество не решенного по товару
     *
     * @param int|null $deltaGoods
     *
     * @return GoodsIssueRegister
     */
    public function setDeltaGoods($deltaGoods = null)
    {
        $this->deltaGoods = $deltaGoods;

        return $this;
    }
    
    // field deltaClient
    
    /**
     * Получить количество не решенного по клиенту
     *
     * @return int|null
     */
    public function getDeltaClient()
    {
        return $this->deltaClient;
    }

    /**
     * Установить количество не решенного по клиенту
     *
     * @param int|null $deltaClient
     *
     * @return GoodsIssueRegister
     */
    public function setDeltaClient($deltaClient = null)
    {
        $this->deltaClient = $deltaClient;

        return $this;
    }

    // field deltaSupplier
    
    /**
     * Получить количество не решенного по поставщику
     *
     * @return int|null
     */
    public function getDeltaSupplier()
    {
        return $this->deltaSupplier;
    }

    /**
     * Установить количество не решенного по поставщику
     *
     * @param int|null $deltaSupplier
     *
     * @return GoodsIssueRegister
     */
    public function setDeltaSupplier($deltaSupplier = null)
    {
        $this->deltaSupplier = $deltaSupplier;

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
        $this->createdAt = new \DateTime;
        $this->deltaClient = 0;
        $this->deltaGoods = 0;
        $this->deltaSupplier = 0;
    }
    
    function __clone()
    {
        $this->id = null;
    }
    
    // </editor-fold>
    
}
