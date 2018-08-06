<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierProductTransferLog
 *
 * @ORM\Table(name="supplier_product_transfer_log")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\SupplierProductTransferLogRepository")
 */
class SupplierProductTransferLog
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
     * @ORM\Column(name="supplier_product_id", type="integer")
     */
    private $supplierProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="transfered_by", type="integer")
     */
    private $transferedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="transfered_at", type="datetime")
     */
    private $transferedAt;


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
     * Set supplierProductId
     *
     * @param integer $supplierProductId
     *
     * @return SupplierProductTransferLog
     */
    public function setSupplierProductId($supplierProductId)
    {
        $this->supplierProductId = $supplierProductId;

        return $this;
    }

    /**
     * Get supplierProductId
     *
     * @return int
     */
    public function getSupplierProductId()
    {
        return $this->supplierProductId;
    }

    /**
     * Set baseProductId
     *
     * @param integer $baseProductId
     *
     * @return SupplierProductTransferLog
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
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
     * Set transferedBy
     *
     * @param integer $transferedBy
     *
     * @return SupplierProductTransferLog
     */
    public function setTransferedBy($transferedBy)
    {
        $this->transferedBy = $transferedBy;

        return $this;
    }

    /**
     * Get transferedBy
     *
     * @return int
     */
    public function getTransferedBy()
    {
        return $this->transferedBy;
    }

    /**
     * Set transferedAt
     *
     * @param \DateTime $transferedAt
     *
     * @return SupplierProductTransferLog
     */
    public function setTransferedAt($transferedAt)
    {
        $this->transferedAt = $transferedAt;

        return $this;
    }

    /**
     * Get transferedAt
     *
     * @return \DateTime
     */
    public function getTransferedAt()
    {
        return $this->transferedAt;
    }
}

