<?php

namespace AccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneCSupply
 *
 * @ORM\Table(name="1_c_supply")
 * @ORM\Entity(repositoryClass="AccountingBundle\Repository\OneCSupplyRepository")
 */
class OneCSupply
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
     * @var int
     *
     * @ORM\Column(name="supply_id", type="integer")
     */
    private $supplyId;

    /**
     * @var string
     *
     * @ORM\Column(name="supplier_invoice_number", type="string", length=255, nullable=true)
     */
    private $supplierInvoiceNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="supplier_invoice_time", type="datetime", nullable=true)
     */
    private $supplierInvoiceTime;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_number", type="string", length=255, nullable=true)
     */
    private $invoiceNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="factura_number", type="string", length=255, nullable=true)
     */
    private $facturaNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="changed_at", type="datetime", nullable=true)
     */
    private $changedAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_closed_period", type="boolean")
     */
    private $isClosedPeriod;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="purchaser_checked_at", type="datetime", nullable=true)
     */
    private $purchaserCheckedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="bookkeeper_checked_at", type="datetime", nullable=true)
     */
    private $bookkeeperCheckedAt;


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
     * Set supplyId
     *
     * @param integer $supplyId
     *
     * @return OneCSupply
     */
    public function setSupplyId($supplyId)
    {
        $this->supplyId = $supplyId;

        return $this;
    }

    /**
     * Get supplyId
     *
     * @return int
     */
    public function getSupplyId()
    {
        return $this->supplyId;
    }

    /**
     * Set supplierInvoiceNumber
     *
     * @param string $supplierInvoiceNumber
     *
     * @return OneCSupply
     */
    public function setSupplierInvoiceNumber($supplierInvoiceNumber)
    {
        $this->supplierInvoiceNumber = $supplierInvoiceNumber;

        return $this;
    }

    /**
     * Get supplierInvoiceNumber
     *
     * @return string
     */
    public function getSupplierInvoiceNumber()
    {
        return $this->supplierInvoiceNumber;
    }

    /**
     * Set supplierInvoiceTime
     *
     * @param \DateTime $supplierInvoiceTime
     *
     * @return OneCSupply
     */
    public function setSupplierInvoiceTime($supplierInvoiceTime)
    {
        $this->supplierInvoiceTime = $supplierInvoiceTime;

        return $this;
    }

    /**
     * Get supplierInvoiceTime
     *
     * @return \DateTime
     */
    public function getSupplierInvoiceTime()
    {
        return $this->supplierInvoiceTime;
    }

    /**
     * Set invoiceNumber
     *
     * @param string $invoiceNumber
     *
     * @return OneCSupply
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * Get invoiceNumber
     *
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * Set facturaNumber
     *
     * @param string $facturaNumber
     *
     * @return OneCSupply
     */
    public function setFacturaNumber($facturaNumber)
    {
        $this->facturaNumber = $facturaNumber;

        return $this;
    }

    /**
     * Get facturaNumber
     *
     * @return string
     */
    public function getFacturaNumber()
    {
        return $this->facturaNumber;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return OneCSupply
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
     * Set changedAt
     *
     * @param \DateTime $changedAt
     *
     * @return OneCSupply
     */
    public function setChangedAt($changedAt)
    {
        $this->changedAt = $changedAt;

        return $this;
    }

    /**
     * Get changedAt
     *
     * @return \DateTime
     */
    public function getChangedAt()
    {
        return $this->changedAt;
    }

    /**
     * Set isClosedPeriod
     *
     * @param boolean $isClosedPeriod
     *
     * @return OneCSupply
     */
    public function setIsClosedPeriod($isClosedPeriod)
    {
        $this->isClosedPeriod = $isClosedPeriod;

        return $this;
    }

    /**
     * Get isClosedPeriod
     *
     * @return bool
     */
    public function getIsClosedPeriod()
    {
        return $this->isClosedPeriod;
    }

    /**
     * Set purchaserCheckedAt
     *
     * @param \DateTime $purchaserCheckedAt
     *
     * @return OneCSupply
     */
    public function setPurchaserCheckedAt($purchaserCheckedAt)
    {
        $this->purchaserCheckedAt = $purchaserCheckedAt;

        return $this;
    }

    /**
     * Get purchaserCheckedAt
     *
     * @return \DateTime
     */
    public function getPurchaserCheckedAt()
    {
        return $this->purchaserCheckedAt;
    }

    /**
     * Set bookkeeperCheckedAt
     *
     * @param \DateTime $bookkeeperCheckedAt
     *
     * @return OneCSupply
     */
    public function setBookkeeperCheckedAt($bookkeeperCheckedAt)
    {
        $this->bookkeeperCheckedAt = $bookkeeperCheckedAt;

        return $this;
    }

    /**
     * Get bookkeeperCheckedAt
     *
     * @return \DateTime
     */
    public function getBookkeeperCheckedAt()
    {
        return $this->bookkeeperCheckedAt;
    }
}

