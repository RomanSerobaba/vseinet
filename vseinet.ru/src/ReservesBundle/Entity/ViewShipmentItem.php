<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ViewShipmentItem
 *
 * @ORM\Table(name="view_shipment_item")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\ViewShipmentItemRepository")
 */
class ViewShipmentItem
{
    const TYPE_RESERVED = 'reserved';
    const TYPE_SUPPLIER = 'supplier';

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
     * @ORM\Column(name="shipment_id", type="integer")
     */
    private $shipmentId;

    /**
     * @var string
     *
     * @ORM\Column(name="shipment_type", type="string", length=255)
     */
    private $shipmentType;


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
     * @return ViewShipmentItem
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
     * Set shipmentId
     *
     * @param integer $shipmentId
     *
     * @return ViewShipmentItem
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
     * Set shipmentType
     *
     * @param string $shipmentType
     *
     * @return ViewShipmentItem
     */
    public function setShipmentType($shipmentType)
    {
        $this->shipmentType = $shipmentType;

        return $this;
    }

    /**
     * Get shipmentType
     *
     * @return string
     */
    public function getShipmentType()
    {
        return $this->shipmentType;
    }
}

