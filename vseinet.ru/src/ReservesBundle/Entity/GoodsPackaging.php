<?php

namespace ReservesBundle\Entity;

use AppBundle\Enum\GoodsPackagingType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Inventory
 *
 * @ORM\Table(name="goods_packaging")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\GoodsPackagingRepository")
 */
class GoodsPackaging
{
    /////////////////////////////////////////////////
    //
    //  Перечисление inventory_status
    //
    
    // <editor-fold defaultstate="collapsed" desc="Поля">
    
    /**
     * @var integer
     *
     * @ORM\Column(name="did", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="any_doc_did_seq")
     */
    private $dId;

    /**
     * @var integer
     *
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(name="title", type="string")
     */
    private $title;

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
     * @var integer
     *
     * @ORM\Column(name="geo_room_id", type="integer")
     */
    private $geoRoomId;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registred_at", type="datetime", nullable=true)
     */
    private $registredAt;
    

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->dId;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return GoodsPackaging
     */
    public function setType($type)
    {
        if ((GoodsPackagingType::COMBINING != $type)&&(GoodsPackagingType::FRACTIONATION != $type)) {
            throw new BadRequestHttpException('Неверный тип документа.');
        }
        $this->type = $type;

        return $this;
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
     * @return GoodsPackaging
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
     * @return GoodsPackaging
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
     * @return GoodsPackaging
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }
    
    /**
     * Установить номер докмуента
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Получить номер докмуента
     *
     * @param int $number
     *
     * @return GoodsPackaging
     */
    public function setNumber(int $number)
    {
        $this->number = $number;

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
     * @param \DateTime|null $completedAt
     *
     * @return GoodsPackaging
     */
    public function setCompletedAt($completedAt = null)
    {
        $this->completedAt = $completedAt;

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
     * @param int|null $completedBy
     *
     * @return GoodsPackaging
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
     * @return GoodsPackaging
     */
    public function setGeoRoomId($geoRoomId)
    {
        $this->geoRoomId = $geoRoomId;

        return $this;
    }

    /**
     * Get baseProductId
     *
     * @return int
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Set baseProductId
     *
     * @param int $baseProductId
     *
     * @return GoodsPackaging
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set quantity
     *
     * @param int $quantity
     *
     * @return GoodsPackaging
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get registredAt
     *
     * @return \DateTime
     */
    public function getRegistredAt()
    {
        return $this->registredAt;
    }

    /**
     * Set registredAt
     *
     * @param \DateTime $registredAt
     *
     * @return GoodsPackaging
     */
    public function setRegistredAt($registredAt)
    {
        $this->registredAt = $registredAt;

        return $this;
    }

    // </editor-fold>
}

