<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DepartmentToDepartment
 *
 * @ORM\Table(name="org_department_to_department")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\DepartmentToDepartmentRepository")
 */
class DepartmentToDepartment
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
     * @ORM\Column(name="org_department_id", type="integer")
     */
    private $departmentId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="pid", type="integer", nullable=true)
     */
    private $pid;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="active_since", type="datetime", nullable=true)
     */
    private $activeSince;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="active_till", type="datetime", nullable=true)
     */
    private $activeTill;

    /**
     * @var int|null
     *
     * @ORM\Column(name="activated_by", type="integer", nullable=true)
     */
    private $activatedBy;


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
     * Set departmentId.
     *
     * @param int $departmentId
     *
     * @return DepartmentToDepartment
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
     * @param int|null $pid
     *
     * @return DepartmentToDepartment
     */
    public function setPid($pid = null)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get pid.
     *
     * @return int|null
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set activeSince.
     *
     * @param \DateTime|null $activeSince
     *
     * @return DepartmentToDepartment
     */
    public function setActiveSince($activeSince = null)
    {
        $this->activeSince = $activeSince;

        return $this;
    }

    /**
     * Get activeSince.
     *
     * @return \DateTime|null
     */
    public function getActiveSince()
    {
        return $this->activeSince;
    }

    /**
     * Set activeTill.
     *
     * @param \DateTime|null $activeTill
     *
     * @return DepartmentToDepartment
     */
    public function setActiveTill($activeTill = null)
    {
        $this->activeTill = $activeTill;

        return $this;
    }

    /**
     * Get activeTill.
     *
     * @return \DateTime|null
     */
    public function getActiveTill()
    {
        return $this->activeTill;
    }

    /**
     * Set activatedBy.
     *
     * @param int|null $activatedBy
     *
     * @return DepartmentToDepartment
     */
    public function setActivatedBy($activatedBy = null)
    {
        $this->activatedBy = $activatedBy;

        return $this;
    }

    /**
     * Get activatedBy.
     *
     * @return int|null
     */
    public function getActivatedBy()
    {
        return $this->activatedBy;
    }
}
