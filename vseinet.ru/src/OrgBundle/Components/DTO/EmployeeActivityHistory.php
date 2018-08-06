<?php

namespace OrgBundle\Components\DTO;

use OrgBundle\Entity\ActivityHistory;
use Symfony\Component\Validator\Constraints as Assert;

class EmployeeActivityHistory
{
    /**
     * @var int
     *
     * @Assert\Type(type="integer")
     */
    public $salaryActivityId;

    /**
     * @var int
     *
     * @Assert\Type(type="integer")
     */
    public $employeeUserId;

    /**
     * @var int
     *
     * @Assert\Type(type="integer")
     */
    public $activityId;

    /**
     * @var \DateTime
     *
     * @Assert\Type(type="string")
     * @Assert\DateTime()
     */
    public $activeSince;

    /**
     * @var \DateTime|null
     *
     * @Assert\Type(type="string")
     * @Assert\DateTime()
     */
    public $activeTill;

    /**
     * @var boolean
     *
     * @Assert\Type(type="boolean")
     */
    public $isPlanned;

    /**
     * @var int
     *
     * @Assert\Type(type="integer")
     */
    public $coefficient;

    /**
     * @var int
     *
     * @Assert\Type(type="integer")
     */
    public $rate;

    /**
     * @var int
     *
     * @Assert\Type(type="integer")
     */
    public $coeff;

    /**
     * @var int
     *
     * @Assert\Type(type="integer")
     */
    public $temp;

    /**
     * @var int
     *
     * @Assert\Type(type="integer")
     */
    public $daysCoeff;

    /**
     * @var int
     *
     * @Assert\Type(type="integer")
     */
    public $salary;

    /**
     * @var ActivityHistory|null
     */
    public $activityHistory;

    /**
     * EmployeeActivityHistory constructor.
     * @param int $salaryActivityId
     * @param int $employeeUserId
     * @param int $activityId
     * @param \DateTime $activeSince
     * @param \DateTime|null $activeTill
     * @param bool $isPlanned
     * @param int $coefficient
     * @param int $rate
     * @param int $coeff
     * @param int $temp
     * @param int $daysCoeff
     * @param int $salary
     * @param null|ActivityHistory $activityHistory
     */
    public function __construct(
        int $salaryActivityId,
        int $employeeUserId,
        int $activityId,
        \DateTime $activeSince,
        ?\DateTime $activeTill,
        bool $isPlanned,
        int $coefficient,
        int $rate,
        int $coeff=0,
        int $temp=0,
        int $daysCoeff=0,
        int $salary=0,
        ?ActivityHistory $activityHistory=null
    )
    {
        $this->salaryActivityId = $salaryActivityId;
        $this->employeeUserId = $employeeUserId;
        $this->activityId = $activityId;
        $this->activeSince = $activeSince;
        $this->activeTill = $activeTill;
        $this->isPlanned = $isPlanned;
        $this->coefficient = $coefficient;
        $this->rate = $rate;
        $this->coeff = $coeff;
        $this->temp = $temp;
        $this->daysCoeff = $daysCoeff;
        $this->salary = $salary;
        $this->activityHistory = $activityHistory;
    }
}