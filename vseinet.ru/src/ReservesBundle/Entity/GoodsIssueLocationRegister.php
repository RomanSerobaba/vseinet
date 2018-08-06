<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Регистр претензий
 *
 * @ORM\Table(name="goods_issue_location_register")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\GoodsIssueLocationRegisterRepository")
 */

class GoodsIssueLocationRegister
{
    
    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    ///////////////////////////
    //
    //  Поля
    //
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer")
     */
    private $createdBy;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="registrator_id", type="integer", nullable=true)
     */
    private $registratorId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="registrator_type_code", type="string")
     */
    private $registratorTypeCode;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registered_at", type="datetime")
     */
    private $registeredAt;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="goods_issue_doc_id", type="integer")
     */
    private $goodsIssueDocId;

    /**
     * @var integer
     *
     * @ORM\Column(name="service_center_id", type="integer", nullable=true)
     */
    private $serviceCenterId;


    /**
     * @var integer
     *
     * @ORM\Column(name="geo_room_id", type="integer", nullable=true)
     */
    private $geoRoomId;

    /**
     * @var integer
     *
     * @ORM\Column(name="delta", type="integer")
     */
    private $delta;

    
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
     * @return GoodsIssueLocationRegister
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
     * @return GoodsIssueLocationRegister
     */
    public function setCreatedBy($createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    // field registratorId

    /**
     * Получить идентификатор документа-регистратора
     *
     * @return int
     */
    public function getRegistratorId()
    {
        return $this->registratorId;
    }

    /**
     * Установить идентификатор документа-регистратора
     *
     * @param int $registratorId
     *
     * @return GoodsIssueLocationRegister
     */
    public function setRegistratorId($registratorId)
    {
        $this->registratorId = $registratorId;

        return $this;
    }

    // field registratorTypeCode

    /**
     * Получить тип документа-регистратора
     *
     * @return string
     */
    public function getRegistratorTypeCode()
    {
        return $this->registratorTypeCode;
    }

    /**
     * Установить тип документа-регистратора
     *
     * @param string $registratorTypeCode
     *
     * @return GoodsIssueLocationRegister
     */
    public function setRegistratorTypeCode($registratorTypeCode)
    {
        $this->registratorTypeCode = $registratorTypeCode;

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
     * @return GoodsIssueLocationRegister
     */
    public function setRegisteredAt($registeredAt)
    {
        $this->registeredAt = $registeredAt;
        
        return $this;
    }
    
    // field goodsIssueDocId
    
    /**
     * Получить идентификатор претензии
     *
     * @return int
     */
    public function getGoodsIssueDocId()
    {
        return $this->goodsIssueDocId;
    }

    /**
     * Установить идентификатор претензии
     *
     * @param int $goodsIssueDocId
     *
     * @return GoodsIssueLocationRegister
     */
    public function setGoodsIssueDocId($goodsIssueDocId)
    {
        $this->goodsIssueDocId = $goodsIssueDocId;

        return $this;
    }
    
    // field serviceCenterId
    
    /**
     * Получить идентификатор сервисного центра
     *
     * @return int
     */
    public function getServiceCenterId()
    {
        return $this->serviceCenterId;
    }

    /**
     * Установить идентификатор сервисного центра
     *
     * @param int $serviceCenterId
     *
     * @return GoodsIssueLocationRegister
     */
    public function setServiceCenterId($serviceCenterId)
    {
        $this->serviceCenterId = $serviceCenterId;

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
     * @return GoodsIssueLocationRegister
     */
    public function setGeoRoomId($geoRoomId)
    {
        $this->geoRoomId = $geoRoomId;

        return $this;
    }

    // field delta
    
    /**
     * Получить количество
     *
     * @return int
     */
    public function getDelta()
    {
        return $this->delta;
    }

    /**
     * Установить количество
     *
     * @param int $delta
     *
     * @return GoodsIssueLocationRegister
     */
    public function setDelta($delta)
    {
        $this->delta = $delta;

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
    }
    
    function __clone()
    {
        $this->id = null;
    }
    
    // </editor-fold>
    
}
