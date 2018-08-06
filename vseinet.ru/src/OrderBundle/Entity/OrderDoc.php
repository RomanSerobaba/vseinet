<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

/**
 * OrderDoc
 *
 * @ORM\Table(name="order_doc")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\OrderDocRepository")
 */
class OrderDoc
{
    use \DocumentBundle\Prototipe\DocumentEntity;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var \DateTime|null
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="registered_at", type="datetime", nullable=true)
     */
    private $registeredAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="registered_by", type="integer", nullable=true)
     */
    private $registeredBy;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="integer")
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="order_id_seq", initialValue=1, allocationSize=1)
     */
    private $number;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer")
     */
    private $geoCityId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_point_id", type="integer")
     */
    private $geoPointId;

    /**
     * @var string
     *
     * @ORM\Column(name="order_type_code", type="string", length=30, nullable=true)
     */
    private $orderTypeCode;

    /**
     * @var int
     *
     * @ORM\Column(name="manager_id", type="integer")
     */
    private $managerId;

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return OrderDoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy.
     *
     * @param int|null $createdBy
     *
     * @return OrderDoc
     */
    public function setCreatedBy($createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return int|null
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set completedAt.
     *
     * @param \DateTime|null $completedAt
     *
     * @return OrderDoc
     */
    public function setCompletedAt($completedAt = null)
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * Get completedAt.
     *
     * @return \DateTime|null
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * Set completedBy.
     *
     * @param int|null $completedBy
     *
     * @return OrderDoc
     */
    public function setCompletedBy($completedBy = null)
    {
        $this->completedBy = $completedBy;

        return $this;
    }

    /**
     * Get completedBy.
     *
     * @return int|null
     */
    public function getCompletedBy()
    {
        return $this->completedBy;
    }

    /**
     * Set registeredAt.
     *
     * @param \DateTime|null $registeredAt
     *
     * @return OrderDoc
     */
    public function setRegisteredAt($registeredAt = null)
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }

    /**
     * Get registeredAt.
     *
     * @return \DateTime|null
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    /**
     * Set registeredBy.
     *
     * @param int|null $registeredBy
     *
     * @return OrderDoc
     */
    public function setRegisteredBy($registeredBy = null)
    {
        $this->registeredBy = $registeredBy;

        return $this;
    }

    /**
     * Get registeredBy.
     *
     * @return int|null
     */
    public function getRegisteredBy()
    {
        return $this->registeredBy;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return OrderDoc
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get number.
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set geoCityId.
     *
     * @param int $geoCityId
     *
     * @return OrderDoc
     */
    public function setGeoCityId($geoCityId)
    {
        $this->geoCityId = $geoCityId;

        return $this;
    }

    /**
     * Get geoCityId.
     *
     * @return int
     */
    public function getGeoCityId()
    {
        return $this->geoCityId;
    }

    /**
     * Set geoPointId.
     *
     * @param int $geoPointId
     *
     * @return OrderDoc
     */
    public function setGeoPointId($geoPointId)
    {
        $this->geoPointId = $geoPointId;

        return $this;
    }

    /**
     * Get geoPointId.
     *
     * @return int
     */
    public function getGeoPointId()
    {
        return $this->geoPointId;
    }

    /**
     * Set orderTypeCode.
     *
     * @param string $orderTypeCode
     *
     * @return OrderDoc
     */
    public function setOrderTypeCode($orderTypeCode)
    {
        $this->orderTypeCode = $orderTypeCode;

        return $this;
    }

    /**
     * Get orderTypeCode.
     *
     * @return string
     */
    public function getOrderTypeCode()
    {
        return $this->orderTypeCode;
    }

    /**
     * Set managerId.
     *
     * @param int $managerId
     *
     * @return OrderDoc
     */
    public function setManagerId($managerId)
    {
        $this->managerId = $managerId;

        return $this;
    }

    /**
     * Get managerId.
     *
     * @return int
     */
    public function getManagerId()
    {
        return $this->managerId;
    }
}
