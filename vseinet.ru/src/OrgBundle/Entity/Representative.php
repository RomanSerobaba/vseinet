<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Representative
 *
 * @ORM\Table(name="representative")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\RepresentativeRepository")
 */
class Representative
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="geo_point_id", type="integer")
     */
    private $geoPointId;

    /**
     * @var int
     *
     * @ORM\Column(name="org_department_id", type="integer")
     */
    private $departmentId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_warehouse", type="boolean")
     */
    private $hasWarehouse;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_retail", type="boolean")
     */
    private $hasRetail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_order_issueing", type="boolean")
     */
    private $hasOrderIssueing;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_delivery", type="boolean")
     */
    private $hasDelivery;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_rising", type="boolean")
     */
    private $hasRising;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="schedule", type="string", length=255, nullable=true)
     */
    private $schedule;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="opening_date", type="datetime", nullable=true)
     */
    private $openingDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ip", type="string", length=255, nullable=true)
     */
    private $ip;

    /**
     * @var int|null
     *
     * @ORM\Column(name="delivery_tax", type="integer", nullable=true)
     */
    private $deliveryTax;

    /**
     * @var int|null
     *
     * @ORM\Column(name="franchiser_id", type="integer", nullable=true)
     */
    private $franchiserId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_central", type="boolean")
     */
    private $isCentral;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_transit", type="boolean")
     */
    private $hasTransit;

    /**
     * Set geoPointId.
     *
     * @param int $geoPointId
     *
     * @return Representative
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
     * Set departmentId.
     *
     * @param int $departmentId
     *
     * @return Representative
     */
    public function setDepartmentId($departmentId)
    {
        $this->departmentId = $departmentId;

        return $this;
    }

    /**
     * Get departmentId.
     *
     * @return int
     */
    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    /**
     * Set hasWarehouse.
     *
     * @param boolean $hasWarehouse
     *
     * @return Representative
     */
    public function setHasWarehouse($hasWarehouse)
    {
        $this->hasWarehouse = $hasWarehouse;

        return $this;
    }

    /**
     * Get hasWarehouse.
     *
     * @return boolean
     */
    public function getHasWarehouse()
    {
        return $this->hasWarehouse;
    }

    /**
     * Set hasRetail.
     *
     * @param boolean $hasRetail
     *
     * @return Representative
     */
    public function setHasRetail($hasRetail)
    {
        $this->hasRetail = $hasRetail;

        return $this;
    }

    /**
     * Get hasRetail.
     *
     * @return boolean
     */
    public function getHasRetail()
    {
        return $this->hasRetail;
    }

    /**
     * Set hasOrderIssueing.
     *
     * @param boolean $hasOrderIssueing
     *
     * @return Representative
     */
    public function setHasOrderIssueing($hasOrderIssueing)
    {
        $this->hasOrderIssueing = $hasOrderIssueing;

        return $this;
    }

    /**
     * Get hasOrderIssueing.
     *
     * @return boolean
     */
    public function getHasOrderIssueing()
    {
        return $this->hasOrderIssueing;
    }

    /**
     * Set hasDelivery.
     *
     * @param boolean $hasDelivery
     *
     * @return Representative
     */
    public function setHasDelivery($hasDelivery)
    {
        $this->hasDelivery = $hasDelivery;

        return $this;
    }

    /**
     * Get hasDelivery.
     *
     * @return boolean
     */
    public function getHasDelivery()
    {
        return $this->hasDelivery;
    }

    /**
     * Set hasRising.
     *
     * @param boolean $hasRising
     *
     * @return Representative
     */
    public function setHasRising($hasRising)
    {
        $this->hasRising = $hasRising;

        return $this;
    }

    /**
     * Get hasRising.
     *
     * @return boolean
     */
    public function getHasRising()
    {
        return $this->hasRising;
    }

    /**
     * Set isActive.
     *
     * @param boolean $isActive
     *
     * @return Representative
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return Representative
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set schedule.
     *
     * @param string|null $schedule
     *
     * @return Representative
     */
    public function setSchedule($schedule = null)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule.
     *
     * @return string|null
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Set openingDate.
     *
     * @param \DateTime|null $openingDate
     *
     * @return Representative
     */
    public function setOpeningDate($openingDate = null)
    {
        $this->openingDate = $openingDate;

        return $this;
    }

    /**
     * Get openingDate.
     *
     * @return \DateTime|null
     */
    public function getOpeningDate()
    {
        return $this->openingDate;
    }

    /**
     * Set ip.
     *
     * @param string|null $ip
     *
     * @return Representative
     */
    public function setIp($ip = null)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip.
     *
     * @return string|null
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set deliveryTax.
     *
     * @param int|null $deliveryTax
     *
     * @return Representative
     */
    public function setDeliveryTax($deliveryTax = null)
    {
        $this->deliveryTax = $deliveryTax;

        return $this;
    }

    /**
     * Get deliveryTax.
     *
     * @return int|null
     */
    public function getDeliveryTax()
    {
        return $this->deliveryTax;
    }

    /**
     * Set franchiserId.
     *
     * @param int|null $franchiserId
     *
     * @return Representative
     */
    public function setFranchiserId($franchiserId = null)
    {
        $this->franchiserId = $franchiserId;

        return $this;
    }

    /**
     * Get franchiserId.
     *
     * @return int|null
     */
    public function getFranchiserId()
    {
        return $this->franchiserId;
    }

    /**
     * @return boolean
     */
    public function getIsCentral()
    {
        return $this->isCentral;
    }

    /**
     * @param boolean $isCentral
     */
    public function setIsCentral($isCentral)
    {
        $this->isCentral = $isCentral;
    }

    /**
     * @return boolean
     */
    public function getHasTransit()
    {
        return $this->hasTransit;
    }

    /**
     * @param boolean $hasTransit
     */
    public function setHasTransit($hasTransit)
    {
        $this->hasTransit = $hasTransit;
    }
}
