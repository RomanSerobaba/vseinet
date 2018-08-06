<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierInvoice
 *
 * @ORM\Table(name="supplier_invoice")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\SupplierInvoiceRepository")
 */
class SupplierInvoice
{
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
     * @var int|null
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var int
     *
     * @ORM\Column(name="supplier_id", type="integer")
     */
    private $supplierId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="supplier_invoice_number", type="string", length=255, nullable=true)
     */
    private $supplierInvoiceNumber;

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
     * @ORM\Column(name="destination_point_id", type="integer")
     */
    private $destinationPointId;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="closed_at", type="datetime", nullable=true)
     */
    private $closedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="closed_by", type="integer", nullable=true)
     */
    private $closedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="arriving_time", type="datetime", nullable=false)
     */
    private $arrivingTime;


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
     * @param \DateTime $createdAt
     *
     * @return SupplierInvoice
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
     * @return SupplierInvoice
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
     * Set supplierId.
     *
     * @param int $supplierId
     *
     * @return SupplierInvoice
     */
    public function setSupplierId($supplierId)
    {
        $this->supplierId = $supplierId;

        return $this;
    }

    /**
     * Get supplierId.
     *
     * @return int
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * Set comment.
     *
     * @param string|null $comment
     *
     * @return SupplierInvoice
     */
    public function setComment($comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string|null
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set supplierInvoiceNumber.
     *
     * @param string|null $supplierInvoiceNumber
     *
     * @return SupplierInvoice
     */
    public function setSupplierInvoiceNumber($supplierInvoiceNumber = null)
    {
        $this->supplierInvoiceNumber = $supplierInvoiceNumber;

        return $this;
    }

    /**
     * Get supplierInvoiceNumber.
     *
     * @return string|null
     */
    public function getSupplierInvoiceNumber()
    {
        return $this->supplierInvoiceNumber;
    }

    /**
     * Set supplierCounteragentId.
     *
     * @param int $supplierCounteragentId
     *
     * @return SupplierInvoice
     */
    public function setSupplierCounteragentId($supplierCounteragentId)
    {
        $this->supplierCounteragentId = $supplierCounteragentId;

        return $this;
    }

    /**
     * Get supplierCounteragentId.
     *
     * @return int
     */
    public function getSupplierCounteragentId()
    {
        return $this->supplierCounteragentId;
    }

    /**
     * Set ourCounteragentId.
     *
     * @param int $ourCounteragentId
     *
     * @return SupplierInvoice
     */
    public function setOurCounteragentId($ourCounteragentId)
    {
        $this->ourCounteragentId = $ourCounteragentId;

        return $this;
    }

    /**
     * Get ourCounteragentId.
     *
     * @return int
     */
    public function getOurCounteragentId()
    {
        return $this->ourCounteragentId;
    }

    /**
     * Set destinationPointId.
     *
     * @param int $destinationPointId
     *
     * @return SupplierInvoice
     */
    public function setDestinationPointId($destinationPointId)
    {
        $this->destinationPointId = $destinationPointId;

        return $this;
    }

    /**
     * Get destinationPointId.
     *
     * @return int
     */
    public function getDestinationPointId()
    {
        return $this->destinationPointId;
    }

    /**
     * Set closedAt.
     *
     * @param \DateTime|null $closedAt
     *
     * @return SupplierInvoice
     */
    public function setClosedAt($closedAt = null)
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    /**
     * Get closedAt.
     *
     * @return \DateTime|null
     */
    public function getClosedAt()
    {
        return $this->closedAt;
    }

    /**
     * Set closedBy.
     *
     * @param int|null $closedBy
     *
     * @return SupplierInvoice
     */
    public function setClosedBy($closedBy = null)
    {
        $this->closedBy = $closedBy;

        return $this;
    }

    /**
     * Get closedBy.
     *
     * @return int|null
     */
    public function getClosedBy()
    {
        return $this->closedBy;
    }

    /**
     * @return \DateTime
     */
    public function getArrivingTime(): \DateTime
    {
        return $this->arrivingTime;
    }

    /**
     * @param \DateTime $arrivingTime
     */
    public function setArrivingTime(\DateTime $arrivingTime)
    {
        $this->arrivingTime = $arrivingTime;
    }
}
