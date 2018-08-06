<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierCategoryPath
 *
 * @ORM\Table(name="supplier_category_path")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\SupplierCategoryPathRepository")
 */
class SupplierCategoryPath
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="pid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $pid;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $level;

    /**
     * @var string
     *
     * @ORM\Column(name="plevel", type="string")
     */
    private $plevel;
    

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return SupplierCategoryPath
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

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
     * Set pid
     *
     * @param integer $pid
     *
     * @return SupplierCategoryPath
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
     * Set level
     *
     * @param integer $level
     *
     * @return SupplierCategoryPath
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set plevel
     *
     * @param string $plevel
     *
     * @return SupplierCategoryPath
     */
    public function setPlevel($plevel)
    {
        $this->plevel = $plevel;

        return $this;
    }

    /**
     * Get plevel
     *
     * @return string
     */
    public function getPlevel()
    {
        return $this->plevel;
    }
}

