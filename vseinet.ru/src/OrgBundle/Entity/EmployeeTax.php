<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeTax
 *
 * @ORM\Table(name="org_employee_tax")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\EmployeeTaxRepository")
 */
class EmployeeTax
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
     * @return EmployeeTax
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
     * @param \DateTime $activeSince
     *
     * @return EmployeeTax
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
     * @return EmployeeTax
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
}
