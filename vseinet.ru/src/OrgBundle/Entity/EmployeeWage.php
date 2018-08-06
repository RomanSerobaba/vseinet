<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeWage
 *
 * @ORM\Table(name="org_employee_wage")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\EmployeeWageRepository")
 */
class EmployeeWage
{
    const PLAN_SOFT = 'soft';
    const PLAN_STRAIGHT = 'straight';

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
     * @var \DateTime|null
     *
     * @ORM\Column(name="active_since", type="date", nullable=true)
     */
    private $activeSince;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="active_till", type="date", nullable=true)
     */
    private $activeTill;

    /**
     * @var int|null
     *
     * @ORM\Column(name="constant_base", type="integer", nullable=true)
     */
    private $constantBase;

    /**
     * @var int|null
     *
     * @ORM\Column(name="plan_base", type="integer", nullable=true)
     */
    private $planBase;

    /**
     * @var string|null
     *
     * @ORM\Column(name="plan_function", type="string", length=255, nullable=true)
     */
    private $planFunction;


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
     * @return EmployeeWage
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
     * Set activeSince.
     *
     * @param \DateTime|null $activeSince
     *
     * @return EmployeeWage
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
     * @return EmployeeWage
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
     * Set constantBase.
     *
     * @param int|null $constantBase
     *
     * @return EmployeeWage
     */
    public function setConstantBase($constantBase = null)
    {
        $this->constantBase = $constantBase;

        return $this;
    }

    /**
     * Get constantBase.
     *
     * @return int|null
     */
    public function getConstantBase()
    {
        return $this->constantBase;
    }

    /**
     * Set planBase.
     *
     * @param int|null $planBase
     *
     * @return EmployeeWage
     */
    public function setPlanBase($planBase = null)
    {
        $this->planBase = $planBase;

        return $this;
    }

    /**
     * Get planBase.
     *
     * @return int|null
     */
    public function getPlanBase()
    {
        return $this->planBase;
    }

    /**
     * Set planFunction.
     *
     * @param string|null $planFunction
     *
     * @return EmployeeWage
     */
    public function setPlanFunction($planFunction = null)
    {
        $this->planFunction = $planFunction;

        return $this;
    }

    /**
     * Get planFunction.
     *
     * @return string|null
     */
    public function getPlanFunction()
    {
        return $this->planFunction;
    }
}
