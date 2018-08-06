<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierCategory
 *
 * @ORM\Table(name="supplier_category")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\SupplierCategoryRepository")
 */
class SupplierCategory
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
     * @var int
     *
     * @ORM\Column(name="pid", type="integer")
     */
    private $pid;

    /**
     * @var int
     * 
     * @ORM\Column(name="sync_category_id", type="integer")
     */
    private $syncCategoryId;

    /**
     * @var bool
     * 
     * @ORM\Column(name="is_hidden", type="boolean")
     */
    private $isHidden;

    /**
     * @var int
     * 
     * @ORM\Column(name="external_id", type="integer")
     */
    private $externalId;


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
     * @return SupplierCategory
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
     * @return SupplierCategory
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
     * Set pid
     *
     * @param integer $pid
     *
     * @return SupplierCategory
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get pid
     *
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set syncCategoryId
     * 
     * @param integer $syncCategoryId
     * 
     * @return SupplierCategory
     */
    public function setSyncCategoryId($syncCategoryId)
    {
        $this->syncCategoryId = $syncCategoryId;

        return $this;
    }

    /**
     * Get syncCategoryId
     * 
     * @return int
     */
    public function getSyncCategoryId()
    {
        return $this->syncCategoryId;
    }

    /**
     * Set isHidden
     * 
     * @param boolean $isHidden
     * 
     * @return SupplierCategory
     */
    public function setIsHidden($isHidden)
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * Get isHidden
     * 
     * @return bool
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }

    /**
     * Set externalId
     * 
     * @param integer $externalId
     * 
     * @return SupplierCategory
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * Get externalId
     * 
     * @return int
     */
    public function getExternalId()
    {
        return $this->externalId;
    }
}

