<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmploymentHistory
 *
 * @ORM\Table(name="org_employment_history")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmploymentHistoryRepository")
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
    private $orgEmployeeUserId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hired_at", type="datetime")
     */
    private $hiredAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fired_at", type="datetime", nullable=true)
     */
    private $firedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="show_till", type="datetime", nullable=true)
     */
    private $showTill;


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
     * Set orgEmployeeUserId
     *
     * @param integer $orgEmployeeUserId
     *
     * @return EmploymentHistory
     */
    public function setOrgEmployeeUserId($orgEmployeeUserId)
    {
        $this->orgEmployeeUserId = $orgEmployeeUserId;

        return $this;
    }

    /**
     * Get orgEmployeeUserId
     *
     * @return int
     */
    public function getOrgEmployeeUserId()
    {
        return $this->orgEmployeeUserId;
    }

    /**
     * Set hiredAt
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
     * Get hiredAt
     *
     * @return \DateTime
     */
    public function getHiredAt()
    {
        return $this->hiredAt;
    }

    /**
     * Set firedAt
     *
     * @param \DateTime $firedAt
     *
     * @return EmploymentHistory
     */
    public function setFiredAt($firedAt)
    {
        $this->firedAt = $firedAt;

        return $this;
    }

    /**
     * Get firedAt
     *
     * @return \DateTime
     */
    public function getFiredAt()
    {
        return $this->firedAt;
    }

    /**
     * Set showTill
     *
     * @param \DateTime $showTill
     *
     * @return EmploymentHistory
     */
    public function setShowTill($showTill)
    {
        $this->showTill = $showTill;

        return $this;
    }

    /**
     * Get showTill
     *
     * @return \DateTime
     */
    public function getShowTill()
    {
        return $this->showTill;
    }
}

