<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryPath
 *
 * @ORM\Table(name="category_path")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\CategoryPathRepository")
 */
class CategoryPath
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
     * @ORM\Column(name="level", type="smallint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $level;

    /**
     * @var int
     *
     * @ORM\Column(name="plevel", type="smallint")
     */
    private $plevel;


    /**
     * Set id
     *
     * @param integer $d
     *
     * @return CategoryPath
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
     * @return CategoryPath
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
     * @return CategoryPath
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
     * @param integer $plevel
     *
     * @return CategoryPath
     */
    public function setPlevel($plevel)
    {
        $this->plevel = $plevel;

        return $this;
    }

    /**
     * Get plevel
     *
     * @return int
     */
    public function getPlevel()
    {
        return $this->plevel;
    }
}

