<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShipmentItem
 *
 * @ORM\Table(name="shipment_item")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\ShipmentItemRepository")
 */
class ShipmentItem
{
    const TYPE_RESERVED = 'reserved';
    const TYPE_ISSUED = 'issued';
    const TYPE_EQUIPMENT = 'equipment';
    const TYPE_WRITTEN_OFF = 'written_off';
    const TYPE_SALE = 'sale';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="shipment_id", type="integer")
     */
    private $shipmentId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="charges", type="integer", nullable=true)
     */
    private $charges;


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
     * Set shipmentId
     *
     * @param integer $shipmentId
     *
     * @return ShipmentItem
     */
    public function setShipmentId($shipmentId)
    {
        $this->shipmentId = $shipmentId;

        return $this;
    }

    /**
     * Get shipmentId
     *
     * @return int
     */
    public function getShipmentId()
    {
        return $this->shipmentId;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return ShipmentItem
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
     * Set charges
     *
     * @param integer $charges
     *
     * @return ShipmentItem
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
}

