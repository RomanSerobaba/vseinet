<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Supply
 *
 * @ORM\Table(name="supply")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\SupplyRepository")
 */
class Supply
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
     * @ORM\Column(name="supplier_id", type="integer")
     */
    private $supplierId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="supplier_counteragent_id", type="integer", nullable=true)
     */
    private $supplierCounteragentId;

    /**
     * @var int
     *
     * @ORM\Column(name="our_counteragent_id", type="integer")
     */
    private $ourCounteragentId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="our_waybill_number", type="string", length=255, nullable=true)
     */
    private $ourWaybillNumber;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="our_waybill_date", type="date", nullable=true)
     */
    private $ourWaybillDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="supplier_waybill_number", type="string", length=255, nullable=true)
     */
    private $supplierWaybillNumber;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="supplier_waybill_date", type="date", nullable=true)
     */
    private $supplierWaybillDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="supplier_invoice_number", type="string", length=255, nullable=true)
     */
    private $supplierInvoiceNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

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
     * @var int
     *
     * @ORM\Column(name="destination_point_id", type="integer")
     */
    private $destinationPointId;


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
     * @return Supply
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
     * @return Supply
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
     * @return Supply
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
     * Set supplierCounteragentId.
     *
     * @param int|null $supplierCounteragentId
     *
     * @return Supply
     */
    public function setSupplierCounteragentId($supplierCounteragentId = null)
    {
        $this->supplierCounteragentId = $supplierCounteragentId;

        return $this;
    }

    /**
     * Get supplierCounteragentId.
     *
     * @return int|null
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
     * @return Supply
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
     * Set ourWaybillNumber.
     *
     * @param string|null $ourWaybillNumber
     *
     * @return Supply
     */
    public function setOurWaybillNumber($ourWaybillNumber = null)
    {
        $this->ourWaybillNumber = $ourWaybillNumber;

        return $this;
    }

    /**
     * Get ourWaybillNumber.
     *
     * @return string|null
     */
    public function getOurWaybillNumber()
    {
        return $this->ourWaybillNumber;
    }

    /**
     * Set ourWaybillDate.
     *
     * @param \DateTime|null $ourWaybillDate
     *
     * @return Supply
     */
    public function setOurWaybillDate($ourWaybillDate = null)
    {
        $this->ourWaybillDate = $ourWaybillDate;

        return $this;
    }

    /**
     * Get ourWaybillDate.
     *
     * @return \DateTime|null
     */
    public function getOurWaybillDate()
    {
        return $this->ourWaybillDate;
    }

    /**
     * Set supplierWaybillNumber.
     *
     * @param string|null $supplierWaybillNumber
     *
     * @return Supply
     */
    public function setSupplierWaybillNumber($supplierWaybillNumber = null)
    {
        $this->supplierWaybillNumber = $supplierWaybillNumber;

        return $this;
    }

    /**
     * Get supplierWaybillNumber.
     *
     * @return string|null
     */
    public function getSupplierWaybillNumber()
    {
        return $this->supplierWaybillNumber;
    }

    /**
     * Set supplierWaybillDate.
     *
     * @param \DateTime|null $supplierWaybillDate
     *
     * @return Supply
     */
    public function setSupplierWaybillDate($supplierWaybillDate = null)
    {
        $this->supplierWaybillDate = $supplierWaybillDate;

        return $this;
    }

    /**
     * Get supplierWaybillDate.
     *
     * @return \DateTime|null
     */
    public function getSupplierWaybillDate()
    {
        return $this->supplierWaybillDate;
    }

    /**
     * Set supplierInvoiceNumber.
     *
     * @param string|null $supplierInvoiceNumber
     *
     * @return Supply
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
     * Set comment.
     *
     * @param string|null $comment
     *
     * @return Supply
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
     * Set registeredAt.
     *
     * @param \DateTime|null $registeredAt
     *
     * @return Supply
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
     * @return Supply
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
     * Set destinationPointId.
     *
     * @param int $destinationPointId
     *
     * @return Supply
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
}
