<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Документ - Претензия - Шапка
 *
 * @ORM\Table(name="goods_issue_moved_doc")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\GoodsIssueMovedDocRepository")
 */

class GoodsIssueMovedDoc
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
     * @var integer
     *
     * @ORM\Column(name="parent_doc_id", type="integer", nullable=true)
     */
    private $parentDocId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="parent_doc_type", type="string", nullable=true)
     */
    private $parentDocType;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="completed_at", type="datetime", nullable=true)
     */
    private $completedAt;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="completed_by", type="integer", nullable=true)
     */
    private $completedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registered_at", type="datetime", nullable=true)
     */
    private $registeredAt;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="registered_by", type="integer", nullable=true)
     */
    private $registeredBy;


    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;
    
    ////////////////////////////////////////////
    
    /**
     * @var string
     *
     * @ORM\Column(name="type_code", type="string")
     */
    private $typeCode;
    
    /**
     * @var int
     *
     * @ORM\Column(name="goods_issues_id", type="integer")
     */
    private $goodsIssueId;
    
    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;
    
    /**
     * @var int
     *
     * @ORM\Column(name="service_center_id", type="integer", nullable=true)
     */
    private $serviceCenterId;
    
    /**
     * @var int
     *
     * @ORM\Column(name="geo_room_id", type="integer", nullable=true)
     */
    private $geoRoomId;
    
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

    // field parentDocId

    /**
     * Получить идентификатор документа-основания (родителя)
     *
     * @return int|null
     */
    public function getParentDocId()
    {
        return $this->parentDocId;
    }

    /**
     * Установить идентификатор документа-основания (родителя)
     *
     * @param int|null $parentDocId
     *
     * @return GoodsIssueMovedDoc
     */
    public function setParentDocId($parentDocId = null)
    {
        $this->parentDocId = $parentDocId;

        return $this;
    }

    // field parentDocType

    /**
     * Получить nтип документа-основания (родителя)
     *
     * @return string|null
     */
    public function getParentDocType()
    {
        return $this->parentDocType;
    }

    /**
     * Установить тип документа-основания (родителя)
     *
     * @param string|null $parentDocId
     *
     * @return GoodsIssueMovedDoc
     */
    public function setParentDocType($parentDocType = null)
    {
        $this->parentDocType = $parentDocType;

        return $this;
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
     * @return GoodsIssueMovedDoc
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
     * @return GoodsIssueMovedDoc
     */
    public function setCreatedBy($createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    // field completedAt
    
    /**
     * Получение даты завершения (закрытия) документа
     *
     * @return \DateTime|null
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * Установка даты завершения (закрытия) документа
     *
     * @param \DateTime|null $completedAt
     *
     * @return GoodsIssueMovedDoc
     */
    public function setCompletedAt($completedAt = null)
    {
        $this->completedAt = $completedAt;
        
        return $this;
    }

    // field completedBy
    
    /**
     * Получить идентификатор пользователя закрывшего документ
     *
     * @return int|null
     */
    public function getCompletedBy()
    {
        return $this->completedBy;
    }

    /**
     * Установить идентификатор пользователя закрывшего докмуент
     *
     * @param int|null $completedBy
     *
     * @return GoodsIssueMovedDoc
     */
    public function setCompletedBy($completedBy = null)
    {
        $this->completedBy = $completedBy;

        return $this;
    }

    // field registeredAt
    
    /**
     * Получение даты проведения документа
     *
     * @return \DateTime|null
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    /**
     * Установка даты проведения документа
     *
     * @param \DateTime|null $registeredAt
     *
     * @return GoodsIssueMovedDoc
     */
    public function setRegisteredAt($registeredAt = null)
    {
        $this->registeredAt = $registeredAt;
        
        return $this;
    }

    // field registeredBy
    
    /**
     * Получить идентификатор пользователя проводившего документ
     *
     * @return int|null
     */
    public function getRegisteredBy()
    {
        return $this->registeredBy;
    }

    /**
     * Установить идентификатор пользователя проводившего докмуент
     *
     * @param int|null $registeredBy
     *
     * @return GoodsIssueMovedDoc
     */
    public function setRegisteredBy($registeredBy = null)
    {
        $this->registeredBy = $registeredBy;

        return $this;
    }

    // field title
    
    /**
     * Получить заголовок (краткое описание)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Установить заголовок (краткое описание)
     *
     * @param string $title
     *
     * @return GoodsIssueMovedDoc
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
    
    ////////////////////////////////////////////////
    
    //field typeCode

    /**
     * Получить тип кода документа
     *
     * @return string
     */
    public function getTypeCode()
    {
        return $this->typeCode;
    }

    /**
     * Установить тип кода документа
     *
     * @param string $typeCode
     *
     * @return GoodsIssueMovedDoc
     */
    public function setTypeCode($typeCode)
    {
        $this->typeCode = $typeCode;

        return $this;
    }
    
    
    //field goodsIssueId

    /**
     * Получить идентификатор документа претензии
     *
     * @return int
     */
    public function getGoodsIssueId()
    {
        return $this->goodsIssueId;
    }

    /**
     * Установить идентификатор документа претензии
     *
     * @param int $goodsIssueId
     *
     * @return GoodsIssueMovedDoc
     */
    public function setGoodsIssueId($goodsIssueId)
    {
        $this->goodsIssueId = $goodsIssueId;

        return $this;
    }
    
    
    //field quantity

    /**
     * Получить идентификатор документа претензии
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->GoodsIssueId;
    }

    /**
     * Установить идентификатор документа претензии
     *
     * @param int $quantity
     *
     * @return GoodsIssueMovedDoc
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }
    
    //field serviceCenterId

    /**
     * Получить идентификатор сервисного центра
     *
     * @return int|null
     */
    public function getServiceCenterId()
    {
        return $this->serviceCenterId;
    }

    /**
     * Установить идентификатор сервисного центра
     *
     * @param int|null $serviceCenterId
     *
     * @return GoodsIssueMovedDoc
     */
    public function setServiceCenterId($serviceCenterId = null)
    {
        $this->serviceCenterId = $serviceCenterId;

        return $this;
    }
    
    //field geoRoomId

    /**
     * Получить идентификатор сервисного центра
     *
     * @return int|null
     */
    public function getGeoRoomId()
    {
        return $this->geoRoomId;
    }

    /**
     * Установить идентификатор сервисного центра
     *
     * @param int|null $geoRoomId
     *
     * @return GoodsIssueMovedDoc
     */
    public function setGeoRoomId($geoRoomId = null)
    {
        $this->geoRoomId = $geoRoomId;

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
