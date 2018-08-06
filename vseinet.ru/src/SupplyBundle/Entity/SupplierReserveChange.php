<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierReserveChange
 *
 * @ORM\Table(name="supplier_reserve_change")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\SupplierReserveChangeRepository")
 */
class SupplierReserveChange
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
     * @ORM\Column(name="supplier_reserve_id", type="integer")
     */
    private $supplierReserveId;


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
     * @return SupplierReserveChange
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
     * @return SupplierReserveChange
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
     * Set supplierReserveId.
     *
     * @param int $supplierReserveId
     *
     * @return SupplierReserveChange
     */
    public function setSupplierReserveId($supplierReserveId)
    {
        $this->supplierReserveId = $supplierReserveId;

        return $this;
    }

    /**
     * Get supplierReserveId.
     *
     * @return int
     */
    public function getSupplierReserveId()
    {
        return $this->supplierReserveId;
    }
}
