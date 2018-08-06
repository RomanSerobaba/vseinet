<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierToCounteragent
 *
 * @ORM\Table(name="supplier_to_counteragent")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\SupplierToCounteragentRepository")
 */
class SupplierToCounteragent
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
     * @var int
     *
     * @ORM\Column(name="counteragent_id", type="integer")
     */
    private $counteragentId;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_main", type="boolean")
     */
    private $isMain;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;


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
     * Set supplierId.
     *
     * @param int $supplierId
     *
     * @return SupplierToCounteragent
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
     * Set counteragentId.
     *
     * @param int $counteragentId
     *
     * @return SupplierToCounteragent
     */
    public function setCounteragentId($counteragentId)
    {
        $this->counteragentId = $counteragentId;

        return $this;
    }

    /**
     * Get counteragentId.
     *
     * @return int
     */
    public function getCounteragentId()
    {
        return $this->counteragentId;
    }

    /**
     * Set isMain.
     *
     * @param bool $isMain
     *
     * @return SupplierToCounteragent
     */
    public function setIsMain($isMain)
    {
        $this->isMain = $isMain;

        return $this;
    }

    /**
     * Get isMain.
     *
     * @return bool
     */
    public function getIsMain()
    {
        return $this->isMain;
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return SupplierToCounteragent
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}
