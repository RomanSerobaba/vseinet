<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Shipment
 *
 * @ORM\Table(name="shipment")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\ShipmentRepository")
 */
class Shipment
{
    const TYPE_SUPPLIER = 'supplier';
    const TYPE_TRANSIT = 'transit';
    const TYPE_DELIVERY = 'delivery';
    const TYPE_POST = 'post';
    const TYPE_TRADING = 'trading';
    const TYPE_ISSUED = 'issued';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="departure_point_id", type="integer")
     */
    private $departurePointId;

    /**
     * @var int
     *
     * @ORM\Column(name="destination_point_id", type="integer", nullable=true)
     */
    private $destinationPointId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="arriving_time", type="datetime", nullable=true)
     */
    private $arrivingTime;

    /**
     * @var int
     *
     * @ORM\Column(name="charges", type="integer")
     */
    private $charges;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
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
     * @ORM\Column(name="packed_at", type="datetime", nullable=true)
     */
    private $packedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="packed_by", type="integer", nullable=true)
     */
    private $packedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="shipped_at", type="datetime", nullable=true)
     */
    private $shippedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="shipped_by", type="integer", nullable=true)
     */
    private $shippedBy;

    /**
     * @var int
     *
     * @ORM\Column(name="accepted_by", type="integer", nullable=true)
     */
    private $acceptedBy;

    /**
     * @var int
     *
     * @ORM\Column(name="courier_id", type="integer", nullable=true)
     */
    private $courierId;

    /**
     * @var int
     *
     * @ORM\Column(name="vehicle_id", type="integer", nullable=true)
     */
    private $vehicleId;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Shipment
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
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
     * Set departurePointId
     *
     * @param integer $departurePointId
     *
     * @return Shipment
     */
    public function setDeparturePointId($departurePointId)
    {
        $this->departurePointId = $departurePointId;

        return $this;
    }

    /**
     * Get departurePointId
     *
     * @return int
     */
    public function getDeparturePointId()
    {
        return $this->departurePointId;
    }

    /**
     * Set destinationPointId
     *
     * @param integer $destinationPointId
     *
     * @return Shipment
     */
    public function setDestinationPointId($destinationPointId)
    {
        $this->destinationPointId = $destinationPointId;

        return $this;
    }

    /**
     * Get destinationPointId
     *
     * @return int
     */
    public function getDestinationPointId()
    {
        return $this->destinationPointId;
    }

    /**
     * Set arrivingTime
     *
     * @param \DateTime $arrivingTime
     *
     * @return Shipment
     */
    public function setArrivingTime($arrivingTime)
    {
        $this->arrivingTime = $arrivingTime;

        return $this;
    }

    /**
     * Get arrivingTime
     *
     * @return \DateTime
     */
    public function getArrivingTime()
    {
        return $this->arrivingTime;
    }

    /**
     * Set charges
     *
     * @param integer $charges
     *
     * @return Shipment
     */
    public function setCharges($charges)
    {
        $this->charges = $charges;

        return $this;
    }

    /**
     * Get charges
     *
     * @return int
     */
    public function getCharges()
    {
        return $this->charges;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Shipment
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return Shipment
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

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
     * Set packedAt
     *
     * @param \DateTime $packedAt
     *
     * @return Shipment
     */
    public function setPackedAt($packedAt)
    {
        $this->packedAt = $packedAt;

        return $this;
    }

    /**
     * Get packedAt
     *
     * @return \DateTime
     */
    public function getPackedAt()
    {
        return $this->packedAt;
    }

    /**
     * Set packedBy
     *
     * @param integer $packedBy
     *
     * @return Shipment
     */
    public function setPackedBy($packedBy)
    {
        $this->packedBy = $packedBy;

        return $this;
    }

    /**
     * Get packedBy
     *
     * @return int
     */
    public function getPackedBy()
    {
        return $this->packedBy;
    }

    /**
     * Set shippedAt
     *
     * @param \DateTime $shippedAt
     *
     * @return Shipment
     */
    public function setShippedAt($shippedAt)
    {
        $this->shippedAt = $shippedAt;

        return $this;
    }

    /**
     * Get shippedAt
     *
     * @return \DateTime
     */
    public function getShippedAt()
    {
        return $this->shippedAt;
    }

    /**
     * Set shippedBy
     *
     * @param integer $shippedBy
     *
     * @return Shipment
     */
    public function setShippedBy($shippedBy)
    {
        $this->shippedBy = $shippedBy;

        return $this;
    }

    /**
     * Get shippedBy
     *
     * @return int
     */
    public function getShippedBy()
    {
        return $this->shippedBy;
    }

    /**
     * Set acceptedBy
     *
     * @param integer $acceptedBy
     *
     * @return Shipment
     */
    public function setAcceptedBy($acceptedBy)
    {
        $this->acceptedBy = $acceptedBy;

        return $this;
    }

    /**
     * Get acceptedBy
     *
     * @return int
     */
    public function getAcceptedBy()
    {
        return $this->acceptedBy;
    }

    /**
     * Set courierId
     *
     * @param integer $courierId
     *
     * @return Shipment
     */
    public function setCourierId($courierId)
    {
        $this->courierId = $courierId;

        return $this;
    }

    /**
     * Get courierId
     *
     * @return int
     */
    public function getCourierId()
    {
        return $this->courierId;
    }

    /**
     * Set vehicleId
     *
     * @param integer $vehicleId
     *
     * @return Shipment
     */
    public function setVehicleId($vehicleId)
    {
        $this->vehicleId = $vehicleId;

        return $this;
    }

    /**
     * Get vehicleId
     *
     * @return int
     */
    public function getVehicleId()
    {
        return $this->vehicleId;
    }
}

