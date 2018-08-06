<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeToDepartment
 *
 * @ORM\Table(name="org_employee_to_department")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\EmployeeToDepartmentRepository")
 */
class EmployeeToDepartment
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
     * @ORM\Column(name="org_employee_user_id", type="integer")
     */
    private $employeeUserId;

    /**
     * @var int
     *
     * @ORM\Column(name="org_department_id", type="integer")
     */
    private $departmentId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="active_since", type="datetime")
     */
    private $activeSince;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="active_till", type="datetime")
     */
    private $activeTill;

    /**
     * @var string
     *
     * @ORM\Column(name="is_synthetic", type="boolean")
     */
    private $isSynthetic;

    /**
     * @var integer
     *
     * @ORM\Column(name="activated_by", type="integer")
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
     * Set employeeUserId.
     *
     * @param int $employeeUserId
     *
     * @return EmployeeToDepartment
     */
    public function setEmployeeUserId($employeeUserId)
    {
        $this->employeeUserId = $employeeUserId;

        return $this;
    }

    /**
     * Get employeeUserId.
     *
     * @return int
     */
    public function getEmployeeUserId()
    {
        return $this->employeeUserId;
    }

    /**
     * Set departmentId.
     *
     * @param int $departmentId
     *
     * @return EmployeeToDepartment
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
     * Set activeSince.
     *
     * @param \DateTime $activeSince
     *
     * @return EmployeeToDepartment
     */
    public function setActiveSince($activeSince)
    {
        $this->activeSince = $activeSince;

        return $this;
    }

    /**
     * Get activeSince.
     *
     * @return \DateTime
     */
    public function getActiveSince()
    {
        return $this->activeSince;
    }

    /**
     * Set activeTill.
     *
     * @param \DateTime $activeTill
     *
     * @return EmployeeToDepartment
     */
    public function setActiveTill($activeTill)
    {
        $this->activeTill = $activeTill;

        return $this;
    }

    /**
     * Get activeTill.
     *
     * @return \DateTime
     */
    public function getActiveTill()
    {
        return $this->activeTill;
    }

    /**
     * Set isSynthetic.
     *
     * @param boolean $isSynthetic
     *
     * @return EmployeeToDepartment
     */
    public function setIsSynthetic($isSynthetic)
    {
        $this->isSynthetic = $isSynthetic;

        return $this;
    }

    /**
     * Get isSynthetic.
     *
     * @return boolean
     */
    public function getIsSynthetic()
    {
        return $this->isSynthetic;
    }

    /**
     * Set activatedBy
     *
     * @param integer|null $activatedBy
     *
     * @return EmployeeToDepartment
     */
    public function setActivatedBy($activatedBy=null)
    {
        $this->activatedBy = $activatedBy;

        return $this;
    }

    /**
     * Get activatedBy
     *
     * @return integer|null
     */
    public function getActivatedBy()
    {
        return $this->activatedBy;
    }
}
