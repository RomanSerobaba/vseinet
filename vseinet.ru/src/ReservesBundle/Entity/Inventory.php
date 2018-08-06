<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inventory
 *
 * @ORM\Table(name="inventory")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\InventoryRepository")
 */
class Inventory
{
    /////////////////////////////////////////////////
    //
    //  Перечисление inventory_status
    //
    
    const INVENTORY_STATUS_CREATED   = 'created';
    const INVENTORY_STATUS_STARTED   = 'started';
    const INVENTORY_STATUS_STOPPED   = 'stopped';
    const INVENTORY_STATUS_COMPLETED = 'completed';
    
    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    /**
     * @var integer
     * @ORM\Column(name="number", type="integer", nullable=true)
     */
    private $number;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @var integer
     * @ORM\Column(name="created_by", type="integer")
     */
    private $createdBy;
    
    /**
     * @var string
     * @ORM\Column(name="title", type="string")
     */
    private $title;
    
    /**
     * @var integer
     * @ORM\Column(name="geo_room_id", type="integer")
     */
    private $geoRoomId;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="responsible_id", type="integer")
     */
    private $responsibleId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    private $status;

    /**
     * @var array
     *
     * @ORM\Column(name="categories", type="json")
     */
    private $categories;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="completed_at", type="datetime", nullable=true)
     */
    private $completedAt;
    
    /**
     * @var integer
     * @ORM\Column(name="did", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="any_doc_did_seq")
     */
    private $dId;

    /**
     * @var integer
     * @ORM\Column(name="parent_doc_did", type="integer")
     */
    private $parentDocumentId;

    /**
     * @var integer
     * @ORM\Column(name="completed_by", type="integer")
     */
    private $completedBy;

    /**
     * @var \DateTime
     * @ORM\Column(name="registered_at", type="datetime")
     */
    private $registeredAt;

    /**
     * @var integer
     * @ORM\Column(name="registered_by", type="integer")
     */
    private $registeredBy;

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    /**
     * Получить уникальный идентификатор документа
     *
     * @return int
     */
    public function getDId()
    {
        return $this->dId;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Inventory
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
     * @return Inventory
     */
    public function setGeoRoomId($geoRoomId)
    {
        $this->geoRoomId = $geoRoomId;

        return $this;
    }

    /**
     * Get created by
     *
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set created by
     *
     * @param int $createdBy
     *
     * @return Inventory
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Set responsibleId
     *
     * @param int $responsibleId
     *
     * @return Inventory
     */
    public function setResponsibleId($responsibleId)
    {
        $this->responsibleId = $responsibleId;

        return $this;
    }

    /**
     * @return int
     */
    public function getResponsibleId(): int
    {
        return $this->responsibleId;
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
     * @return Inventory
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    
    /**
     * Get categories
     *
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set categories
     *
     * @param array $categories
     *
     * @return Inventory
     */
    public function setCategories(array $categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Get completedAt
     *
     * @return \DateTime
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * Set completedAt
     *
     * @param \DateTime $completedAt
     *
     * @return Inventory
     */
    public function setCompletedAt($completedAt)
    {
        $this->completedAt = $completedAt;
        
        return $this;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Inventory
     */
    public function setStatus($status)
    {
        $this->status = $status;
        
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get parentDocumentId
     *
     * @return int
     */
    public function getParentDocumentId()
    {
        return $this->parentDocumentId;
    }

    /**
     * Set parentDocumentId
     *
     * @param int $parentDocumentId
     *
     * @return Inventory
     */
    public function setParentDocumentId($parentDocumentId)
    {
        $this->parentDocumentId = $parentDocumentId;

        return $this;
    }

    /**
     * Get registeredBy
     *
     * @return int
     */
    public function getRegisteredBy()
    {
        return $this->registeredBy;
    }

    /**
     * Set registeredBy
     *
     * @param int $registeredBy
     *
     * @return Inventory
     */
    public function setRegisteredBy($registeredBy)
    {
        $this->registeredBy = $registeredBy;

        return $this;
    }

    /**
     * Get completedBy
     *
     * @return int
     */
    public function getCompletedBy()
    {
        return $this->completedBy;
    }

    /**
     * Set completedBy
     *
     * @param int $completedBy
     *
     * @return Inventory
     */
    public function setCompletedBy($completedBy)
    {
        $this->completedBy = $completedBy;

        return $this;
    }

    /**
     * Получить номер документа
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Установить номер документа
     *
     * @param int $number
     *
     * @return Inventory
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get registeredAt
     *
     * @return \DateTime
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    /**
     * Set registeredAt
     *
     * @param \DateTime $registeredAt
     *
     * @return Inventory
     */
    public function setRegisteredAt($registeredAt)
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }



    // </editor-fold>
}
