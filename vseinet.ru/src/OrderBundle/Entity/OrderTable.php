<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderTable
 *
 * @ORM\Table(name="`order`")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\OrderTableRepository")
 */
class OrderTable
{
    const GEO_CITY_ID_RUSSIA = -1;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var int
     *
     * @ORM\Column(name="manager_id", type="integer")
     */
    private $managerId;

    /**
     * @var int
     *
     * @ORM\Column(name="our_seller_counteragent_id", type="integer")
     */
    private $ourSellerCounteragentId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="geo_point_id", type="integer", nullable=true)
     */
    private $geoPointId;

    /**
     * @var string
     *
     * @ORM\Column(name="type_code", type="string", length=255)
     */
    private $typeCode;

    /**
     * @var int|null
     *
     * @ORM\Column(name="geo_city_id", type="integer", nullable=true)
     */
    private $geoCityId;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime|null $createdAt
     *
     * @return OrderTable
     */
    public function setCreatedAt($createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime|null
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
     * @return OrderTable
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
     * Set managerId.
     *
     * @param int $managerId
     *
     * @return OrderTable
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

    /**
     * Set ourSellerCounteragentId.
     *
     * @param int $ourSellerCounteragentId
     *
     * @return OrderTable
     */
    public function setOurSellerCounteragentId($ourSellerCounteragentId)
    {
        $this->ourSellerCounteragentId = $ourSellerCounteragentId;

        return $this;
    }

    /**
     * Get ourSellerCounteragentId.
     *
     * @return int
     */
    public function getOurSellerCounteragentId()
    {
        return $this->ourSellerCounteragentId;
    }

    /**
     * Set geoPointId.
     *
     * @param int|null $geoPointId
     *
     * @return OrderTable
     */
    public function setGeoPointId($geoPointId = null)
    {
        $this->geoPointId = $geoPointId;

        return $this;
    }

    /**
     * Get geoPointId.
     *
     * @return int|null
     */
    public function getGeoPointId()
    {
        return $this->geoPointId;
    }

    /**
     * Set typeCode.
     *
     * @param string $typeCode
     *
     * @return OrderTable
     */
    public function setTypeCode($typeCode)
    {
        $this->typeCode = $typeCode;

        return $this;
    }

    /**
     * Get typeCode.
     *
     * @return string
     */
    public function getTypeCode()
    {
        return $this->typeCode;
    }

    /**
     * Set geoCityId.
     *
     * @param int|null $geoCityId
     *
     * @return OrderTable
     */
    public function setGeoCityId($geoCityId = null)
    {
        $this->geoCityId = $geoCityId;

        return $this;
    }

    /**
     * Get geoCityId.
     *
     * @return int|null
     */
    public function getGeoCityId()
    {
        return $this->geoCityId;
    }

    /**
     * Set registeredAt.
     *
     * @param \DateTime|null $registeredAt
     *
     * @return OrderTable
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
     * @return OrderTable
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
     * Set completedAt.
     *
     * @param \DateTime|null $completedAt
     *
     * @return OrderTable
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
     * @return OrderTable
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
}
