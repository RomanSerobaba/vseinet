<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmploymentHistory
 *
 * @ORM\Table(name="org_employment_history")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\EmploymentHistoryRepository")
 */
class EmploymentHistory
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
     * @ORM\Column(name="hired_at", type="date")
     */
    private $hiredAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fired_at", type="date", nullable=true)
     */
    private $firedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="show_till", type="date", nullable=true)
     */
    private $showTill;


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
     * @return EmploymentHistory
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
     * Set hiredAt.
     *
     * @param \DateTime $hiredAt
     *
     * @return EmploymentHistory
     */
    public function setHiredAt($hiredAt)
    {
        $this->hiredAt = $hiredAt;

        return $this;
    }

    /**
     * Get hiredAt.
     *
     * @return \DateTime
     */
    public function getHiredAt()
    {
        return $this->hiredAt;
    }

    /**
     * Set firedAt.
     *
     * @param \DateTime|null $firedAt
     *
     * @return EmploymentHistory
     */
    public function setFiredAt($firedAt = null)
    {
        $this->firedAt = $firedAt;

        return $this;
    }

    /**
     * Get firedAt.
     *
     * @return \DateTime|null
     */
    public function getFiredAt()
    {
        return $this->firedAt;
    }

    /**
     * Set showTill.
     *
     * @param \DateTime|null $showTill
     *
     * @return EmploymentHistory
     */
    public function setShowTill($showTill = null)
    {
        $this->showTill = $showTill;

        return $this;
    }

    /**
     * Get showTill.
     *
     * @return \DateTime|null
     */
    public function getShowTill()
    {
        return $this->showTill;
    }
}
