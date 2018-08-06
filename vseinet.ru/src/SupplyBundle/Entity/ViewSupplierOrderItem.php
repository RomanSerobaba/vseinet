<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ViewSupplierOrderItem
 *
 * @ORM\Table(name="view_supplier_order_item")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\ViewSupplierOrderItemRepository")
 */
class ViewSupplierOrderItem
{
    const TYPE_ORDER = 'order';
    const TYPE_REQUEST = 'request';

    const TYPE_RESERVED = 'reserved';
    const TYPE_SALE = 'sale';
    const TYPE_EQUIPMENT = 'equipment';
    const TYPE_ISSUE = 'issue';

    const RESERVE_STATUS_SHIPPED = 'shipped';
    const RESERVE_STATUS_RESERVED = 'reserved';

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
     * @ORM\Column(name="type", type="string", length=128)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="supplier_id", type="integer")
     */
    private $supplierId;

    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;

    /**
     * @var string
     *
     * @ORM\Column(name="reserve_status", type="string", length=128)
     */
    private $reserveStatus;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_point_id", type="integer")
     */
    private $geoPointId;

    /**
     * @var int
     *
     * @ORM\Column(name="our_seller_counteragent_id", type="integer")
     */
    private $ourSellerCounteragentId;

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
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
     * Get baseProductId
     *
     * @return int
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Get reserveStatus
     *
     * @return string
     */
    public function getReserveStatus()
    {
        return $this->reserveStatus;
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
     * Get geoPointId
     *
     * @return int
     */
    public function getGeoPointId()
    {
        return $this->geoPointId;
    }

    /**
     * @return int
     */
    public function getOurSellerCounteragentId(): int
    {
        return $this->ourSellerCounteragentId;
    }

    /**
     * @param int $ourSellerCounteragentId
     */
    public function setOurSellerCounteragentId(int $ourSellerCounteragentId)
    {
        $this->ourSellerCounteragentId = $ourSellerCounteragentId;
    }
}

