<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierPricelist
 *
 * @ORM\Table(name="supplier_pricelist")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\SupplierPricelistRepository")
 */
class SupplierPricelist
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
     * @ORM\Column(name="supplier_id", type="integer")
     */
    private $supplierId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_multi", type="boolean")
     */
    private $isMulti;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="uploaded_at", type="datetime")
     */
    private $uploadedAt;

    /**
     * @var int
     * 
     * @ORM\Column(name="uploaded_quantity", type="integer")
     */
    private $uploadedQuantity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="upload_started_at", type="datetime")
     */
    private $uploadStartedAt;

    /**
     * @var bool
     * 
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var string
     */
    public $filename;


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
     * Set supplierId
     *
     * @param integer $supplierId
     *
     * @return SupplierPricelist
     */
    public function setSupplierId($supplierId)
    {
        $this->supplierId = $supplierId;

        return $this;
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
     * Set name
     *
     * @param string $name
     *
     * @return SupplierPricelist
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set isMulti
     *
     * @param boolean $isMulti
     *
     * @return SupplierPricelist
     */
    public function setIsMulti($isMulti)
    {
        $this->isMulti = $isMulti;

        return $this;
    }

    /**
     * Get isMulti
     *
     * @return bool
     */
    public function getIsMulti()
    {
        return $this->isMulti;
    }

    /**
     * Set uploadedAt
     *
     * @param \DateTime $uploadedAt
     *
     * @return SupplierPricelist
     */
    public function setUploadedAt($uploadedAt)
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }

    /**
     * Get uploadedAt
     *
     * @return \DateTime
     */
    public function getUploadedAt()
    {
        return $this->uploadedAt;
    }

    /**
     * Set uploadedQuantity
     * 
     * @param integer $uploadedQuantity
     * 
     * @return SupplierPricelist
     */
    public function setUploadedQuantity($uploadedQuantity)
    {
        $this->uploadedQuantity = $uploadedQuantity;

        return $this;
    }

    /**
     * Get uploadedQuantity
     * 
     * @return int
     */
    public function getUploadedQuantity()
    {
        return $this->uploadedQuantity;
    }

    /**
     * Set uploadStartedAt
     *
     * @param \DateTime $uploadStartedAt
     *
     * @return SupplierPricelist
     */
    public function setUploadStartedAt($uploadStartedAt)
    {
        $this->uploadStartedAt = $uploadStartedAt;

        return $this;
    }

    /**
     * Get uploadStartedAt
     *
     * @return \DateTime
     */
    public function getUploadStartedAt()
    {
        return $this->uploadStartedAt;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return SupplierPricelist
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}