<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ViewShipment
 *
 * @ORM\Table(name="view_shipment")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\ViewShipmentRepository")
 */
class ViewShipment
{
    const TYPE_SUPPLIER = 'supplier';
    const TYPE_TRANSIT = 'transit';
    const TYPE_POST = 'post';
    const TYPE_ISSUED = 'issued';
    const TYPE_TRANSPORT = 'transport';
    const TYPE_COURIER = 'courier';

    const STATUS_ARRIVED = 'arrived';
    const STATUS_TRANSIT = 'transit';
    const STATUS_PACKING = 'packing';
    const STATUS_FORMING = 'forming';

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
     * @ORM\Column(name="destination_point_id", type="integer")
     */
    private $destinationPointId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="arriving_time", type="datetime")
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
     * @ORM\Column(name="packed_at", type="datetime")
     */
    private $packedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="packed_by", type="integer")
     */
    private $packedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="shipped_at", type="datetime")
     */
    private $shippedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="shipped_by", type="integer")
     */
    private $shippedBy;

    /**
     * @var int
     *
     * @ORM\Column(name="accepted_by", type="integer")
     */
    private $acceptedBy;

    /**
     * @var int
     *
     * @ORM\Column(name="courier_id", type="integer")
     */
    private $courierId;

    /**
     * @var int
     *
     * @ORM\Column(name="vehicle_id", type="integer")
     */
    private $vehicleId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="accepted_at", type="datetime")
     */
    private $acceptedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="departure_point", type="string", length=255)
     */
    private $departurePoint;

    /**
     * @var string
     *
     * @ORM\Column(name="destination_point", type="string", length=255)
     */
    private $destinationPoint;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;


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
     * @return ViewShipment
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
     * @return ViewShipment
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
     * @return ViewShipment
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
     * @return ViewShipment
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
     * @return ViewShipment
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
     * @return ViewShipment
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
     * @return ViewShipment
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
     * @return ViewShipment
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
     * @return ViewShipment
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
     * @return ViewShipment
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
     * @return ViewShipment
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
     * @return ViewShipment
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
     * @return ViewShipment
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
     * @return ViewShipment
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

    /**
     * Set acceptedAt
     *
     * @param \DateTime $acceptedAt
     *
     * @return ViewShipment
     */
    public function setAcceptedAt($acceptedAt)
    {
        $this->acceptedAt = $acceptedAt;

        return $this;
    }

    /**
     * Get acceptedAt
     *
     * @return \DateTime
     */
    public function getAcceptedAt()
    {
        return $this->acceptedAt;
    }

    /**
     * Set departurePoint
     *
     * @param string $departurePoint
     *
     * @return ViewShipment
     */
    public function setDeparturePoint($departurePoint)
    {
        $this->departurePoint = $departurePoint;

        return $this;
    }

    /**
     * Get departurePoint
     *
     * @return string
     */
    public function getDeparturePoint()
    {
        return $this->departurePoint;
    }

    /**
     * Set destinationPoint
     *
     * @param string $destinationPoint
     *
     * @return ViewShipment
     */
    public function setDestinationPoint($destinationPoint)
    {
        $this->destinationPoint = $destinationPoint;

        return $this;
    }

    /**
     * Get destinationPoint
     *
     * @return string
     */
    public function getDestinationPoint()
    {
        return $this->destinationPoint;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return ViewShipment
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
}

