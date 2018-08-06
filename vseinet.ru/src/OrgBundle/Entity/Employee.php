<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Employee
 *
 * @ORM\Table(name="org_employee")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\EmployeeRepository")
 */
class Employee
{
    const POSITION_CHIEF     = 'chief';
    const POSITION_DEPUTY    = 'deputy';
    const POSITION_EXECUTIVE = 'executive';

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string")
     */
    private $position;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer")
     */
    private $sortOrder;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="clock_in_time", type="datetime", nullable=true)
     */
    private $clockInTime;

    /**
     * @var int
     *
     * @ORM\Column(name="working_hours_weekly", type="integer")
     */
    private $workingHoursWeekly;

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Employee
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set sortOrder
     *
     * @param integer $sortOrder
     *
     * @return Employee
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Set position
     *
     * @param string $position
     *
     * @return Employee
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set clockInTime.
     *
     * @param \DateTime|null $clockInTime
     *
     * @return Employee
     */
    public function setClockInTime($clockInTime = null)
    {
        $this->clockInTime = $clockInTime;

        return $this;
    }

    /**
     * Get clockInTime.
     *
     * @return \DateTime|null
     */
    public function getClockInTime()
    {
        return $this->clockInTime;
    }

    /**
     * Set workingHoursWeekly
     *
     * @param integer $workingHoursWeekly
     *
     * @return Employee
     */
    public function setWorkingHoursWeekly($workingHoursWeekly)
    {
        $this->workingHoursWeekly = $workingHoursWeekly;

        return $this;
    }

    /**
     * Get workingHoursWeekly
     *
     * @return int
     */
    public function getWorkingHoursWeekly()
    {
        return $this->workingHoursWeekly;
    }
}
