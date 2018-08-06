<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeSalaryActivity
 *
 * @ORM\Table(name="org_employee_salary_activity")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\EmployeeSalaryActivityRepository")
 */
class EmployeeSalaryActivity
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
     * @ORM\Column(name="org_activity_id", type="integer")
     */
    private $activityId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="active_since", type="date")
     */
    private $activeSince;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="active_till", type="date", nullable=true)
     */
    private $activeTill;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_planned", type="boolean")
     */
    private $isPlanned;

    /**
     * @var int|null
     *
     * @ORM\Column(name="coefficient", type="integer", nullable=true)
     */
    private $coefficient;

    /**
     * @var int|null
     *
     * @ORM\Column(name="rate", type="integer", nullable=true)
     */
    private $rate;


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
     * @return EmployeeSalaryActivity
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
     * Set activityId.
     *
     * @param int $activityId
     *
     * @return EmployeeSalaryActivity
     */
    public function setActivityId($activityId)
    {
        $this->activityId = $activityId;

        return $this;
    }

    /**
     * Get activityId.
     *
     * @return int
     */
    public function getActivityId()
    {
        return $this->activityId;
    }

    /**
     * Set activeSince.
     *
     * @param \DateTime $activeSince
     *
     * @return EmployeeSalaryActivity
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
     * @param \DateTime|null $activeTill
     *
     * @return EmployeeSalaryActivity
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
     * Set isPlanned.
     *
     * @param bool $isPlanned
     *
     * @return EmployeeSalaryActivity
     */
    public function setIsPlaned($isPlanned)
    {
        $this->isPlanned = $isPlanned;

        return $this;
    }

    /**
     * Get isPlanned.
     *
     * @return bool
     */
    public function getIsPlanned()
    {
        return $this->isPlanned;
    }

    /**
     * Set coefficient.
     *
     * @param int|null $coefficient
     *
     * @return EmployeeSalaryActivity
     */
    public function setCoefficient($coefficient = null)
    {
        $this->coefficient = $coefficient;

        return $this;
    }

    /**
     * Get coefficient.
     *
     * @return int|null
     */
    public function getCoefficient()
    {
        return $this->coefficient;
    }

    /**
     * Set rate.
     *
     * @param int|null $rate
     *
     * @return EmployeeSalaryActivity
     */
    public function setRate($rate = null)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate.
     *
     * @return int|null
     */
    public function getRate()
    {
        return $this->rate;
    }
}
