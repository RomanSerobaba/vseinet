<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ViewSupply
 *
 * @ORM\Table(name="view_supply")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\ViewSupplyRepository")
 */
class ViewSupply
{
    const STATUS_FORMING = 'forming';
    const STATUS_TRANSIT = 'transit';
    const STATUS_ARRIVED = 'arrived';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @var int
     *
     * @ORM\Column(name="supplier_id", type="integer")
     */
    private $supplierId;

    /**
     * @var string
     *
     * @ORM\Column(name="bonus_amount", type="integer")
     */
    private $bonusAmount;

    /**
     * @var int
     *
     * @ORM\Column(name="supplier_counteragent_id", type="integer")
     */
    private $supplierCounteragentId;

    /**
     * @var int
     *
     * @ORM\Column(name="our_counteragent_id", type="integer")
     */
    private $ourCounteragentId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_point_id", type="integer")
     */
    private $geoPointId;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="shipment_ids", type="string", length=255)
     */
    private $shipmentIds;


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
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
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
     * Get supplierId
     *
     * @return int
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * Get bonusAmount
     *
     * @return string
     */
    public function getBonusAmount()
    {
        return $this->bonusAmount;
    }

    /**
     * Get supplierCounteragentId
     *
     * @return int
     */
    public function getSupplierCounteragentId()
    {
        return $this->supplierCounteragentId;
    }

    /**
     * Get ourCounteragentId
     *
     * @return int
     */
    public function getOurCounteragentId()
    {
        return $this->ourCounteragentId;
    }

    /**
     * Get geoPointId
     *
     * @return int
     */
    public function getGeoPointId()
    {
        return $this->geoPointId;
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
     * Get shipmentIds
     *
     * @return string
     */
    public function getShipmentIds()
    {
        return $this->shipmentIds;
    }
}

