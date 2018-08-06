<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DepartmentPath
 *
 * @ORM\Table(name="org_department_path")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\DepartmentPathRepository")
 */
class DepartmentPath
{
    /**
     * @var int
     *
     * @ORM\Column(name="org_department_id", type="integer")
     * @ORM\Id
     */
    private $departmentId;

    /**
     * @var int
     *
     * @ORM\Column(name="pid", type="integer")
     * @ORM\Id
     */
    private $pid;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="smallint")
     */
    private $level;

    /**
     * @var int|null
     *
     * @ORM\Column(name="plevel", type="smallint", nullable=true)
     */
    private $plevel;


    /**
     * Set departmentId.
     *
     * @param int $departmentId
     *
     * @return DepartmentPath
     */
    public function setDepartmentId($departmentId)
    {
        $this->departmentId = $departmentId;

        return $this;
    }

    /**
     * Get departmentId.
     *
     * @return int
     */
    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    /**
     * Set pid.
     *
     * @param int $pid
     *
     * @return DepartmentPath
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get pid.
     *
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set level.
     *
     * @param int $level
     *
     * @return DepartmentPath
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level.
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set plevel.
     *
     * @param int|null $plevel
     *
     * @return DepartmentPath
     */
    public function setPlevel($plevel = null)
    {
        $this->plevel = $plevel;

        return $this;
    }

    /**
     * Get plevel.
     *
     * @return int|null
     */
    public function getPlevel()
    {
        return $this->plevel;
    }
}
