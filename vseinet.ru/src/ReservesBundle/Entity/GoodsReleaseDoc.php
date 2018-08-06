<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * GoodsReleaseDoc
 *
 * @ORM\Table(name="goods_release_doc")
 * @ORM\Entity()
 */
class GoodsReleaseDoc
{

    const STATUS_NEW = 'new';
    const STATUS_COMPLETED = 'completed';

    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    /**
     * @var string
     *
     * @ORM\Column(name="goods_release_type", type="string")
     */
    private $goodsReleaseType;
    
    /**
     * @var int
     *
     * @ORM\Column(name="geo_room_id", type="integer")
     */
    private $geoRoomId;

    /**
     * @var int
     *
     * @ORM\Column(name="destination_room_id", type="integer")
     */
    private $destinationRoomId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @var int
     *
     * @ORM\Column(name="created_by", type="integer")
     */
    private $createdBy;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="completed_at", type="datetime", nullable=true)
     */
    private $completedAt;
    
    /**
     * @var int|null
     *
     * @ORM\Column(name="completed_by", type="integer", nullable=true)
     */
    private $completedBy;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_waiting", type="boolean")
     */
    private $isWaiting;

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
     * @var int
     *
     * @ORM\Column(name="did", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="any_doc_did_seq")
     */
    private $dId;
    
    /**
     * @var int
     *
     * @ORM\Column(name="parent_doc_did", type="integer")
     */
    private $parentDocumentId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var string
     * @ORM\Column(name="status_code", type="string")
     */
    private $statusCode;
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    /**
     * Получить уникальный идентификатор документа
     * @return int
     */
    public function getDId()
    {
        return $this->dId;
    }

    /**
     * Получить номер документа
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Установить номер документа
     * @param int $number
     * @return GoodsRelease
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get created date and time
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set created date and time
     *
     * @param \DateTime $createdAt
     *
     * @return GoodsRelease
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    
    /**
     * Get createdBy
     *
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdBy
     *
     * @param int $createdBy
     *
     * @return GoodsRelease
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }
    
    
    /**
     * Get parenet document did
     *
     * @return int
     */
    public function getParentDocumentId()
    {
        return $this->parentDocumentId;
    }

    /**
     * Set parent document did
     *
     * @param int $parentDocumentId
     *
     * @return GoodsRelease
     */
    public function setParentDocumentId($parentDocumentId)
    {
        $this->parentDocumentId = $parentDocumentId;

        return $this;
    }
    
    /**
     * Get completed date and time
     *
     * @return \DateTime
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * Set completed date and time
     *
     * @param \DateTime $completedAt
     *
     * @return GoodsRelease
     */
    public function setCompletedAt($completedAt)
    {
        $this->completedAt = $completedAt;

        return $this;
    }
    
    /**
     * Get goods release type
     *
     * @return string
     */
    public function getGoodsReleaseType()
    {
        return $this->goodsReleaseType;
    }

    /**
     * Set goods release type
     *
     * @param string $goodsReleaseType
     *
     * @return GoodsRelease
     */
    public function setGoodsReleaseType($goodsReleaseType)
    {
        $this->goodsReleaseType = $goodsReleaseType;
           
        return $this;
    }
    
    /**
     * Get goods release title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set goods release title
     *
     * @param string $title
     *
     * @return GoodsRelease
     */
    public function setTitle($title)
    {
        $this->title = $title;
           
        return $this;
    }
    
    /**
     * Get completedBy
     *
     * @return int|null
     */
    public function getCompletedBy()
    {
        return $this->completedBy;
    }

    /**
     * Set completedBy
     *
     * @param int|null $completedBy
     *
     * @return GoodsRelease
     */
    public function setCompletedBy($completedBy = null)
    {
        $this->completedBy = $completedBy;

        return $this;
    }
    
    /**
     * Get geoRoomId
     *
     * @return int
     */
    public function getGeoRoomId()
    {
        return $this->geoRoomId;
    }

    /**
     * Set geoRoomId
     *
     * @param int $geoRoomId
     *
     * @return GoodsRelease
     */
    public function setGeoRoomId($geoRoomId)
    {
        $this->geoRoomId = $geoRoomId;

        return $this;
    }

    /**
     * Get is waiting status
     *
     * @return boolean
     */
    public function getIsWaiting()
    {
        return $this->geoRoomId;
    }

    /**
     * Set is waiting status
     *
     * @param boolean $isWaiting
     *
     * @return GoodsRelease
     */
    public function setIsWaiting($isWaiting)
    {
        $this->isWaiting = $isWaiting;

        return $this;
    }

    /**
     * Получить идентификатор склада-получателя
     *
     * @return int
     */
    public function getDestinationRoomId()
    {
        return $this->destinationRoomId;
    }

    /**
     * Установать идентификатор склада-получателя
     *
     * @param int $destinationRoomId
     *
     * @return GoodsRelease
     */
    public function setDestinationRoomId($destinationRoomId)
    {
        $this->destinationRoomId = $destinationRoomId;

        return $this;
    }

    /**
     * Get registered date and time
     *
     * @return \DateTime|null
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    /**
     * Set registered date and time
     *
     * @param \DateTime|null $registeredAt
     *
     * @return GoodsRelease
     */
    public function setRegisteredAt($registeredAt = null)
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }

    /**
     * Get registered by
     *
     * @return int|null
     */
    public function getRegisteredBy()
    {
        return $this->registeredBy;
    }

    /**
     * Set registered by
     *
     * @param int|null $registeredBy
     *
     * @return GoodsRelease
     */
    public function setRegisteredBy($registeredBy = null)
    {
        $this->registeredBy = $registeredBy;

        return $this;
    }

    /**
     * Получить статус документа
     *
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Установить статус документа
     *
     * @param string $statusCode
     *
     * @return GoodsRelease
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    // </editor-fold>

    public function __construct()
    {

        $this->isWaiting = false;
        
    }

}

