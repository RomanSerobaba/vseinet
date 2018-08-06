<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeFine
 *
 * @ORM\Table(name="org_employee_fine")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\EmployeeFineRepository")
 */
class EmployeeFine
{
    const TYPE_ABSENCE    = 'absence';
    const TYPE_UNWORKING  = 'unworking';
    const TYPE_OVERTIME   = 'overtime';
    const TYPE_MISCELLANEOUS = 'miscellaneous';

    const STATUS_CREATED  = 'created';
    const STATUS_APPLIED  = 'applied';
    const STATUS_CANCELED = 'canceled';

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
    private $employeeId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="time", type="time", nullable=true)
     */
    private $time;

    /**
     * @var int|null
     *
     * @ORM\Column(name="amount", type="integer", nullable=true)
     */
    private $amount;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cause", type="text", nullable=true)
     */
    private $cause;

    /**
     * @var string|null
     *
     * @ORM\Column(name="auto_cause", type="text", nullable=true)
     */
    private $autoCause;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_hidden", type="boolean", nullable=true)
     */
    private $isHidden;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var int|null
     *
     * @ORM\Column(name="status_changed_by", type="integer", nullable=true)
     */
    private $statusChangedBy;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="status_changed_at", type="datetime", nullable=true)
     */
    private $statusChangedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="approved_by", type="integer", nullable=true)
     */
    private $approvedBy;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="approved_at", type="datetime", nullable=true)
     */
    private $approvedAt;

    /**
     * @var EmployeeCancelRequest|null
     *
     * @ORM\OneToOne(targetEntity="EmployeeCancelRequest", mappedBy="fine")
     */
    private $cancelRequest;


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
     * Set employeeId.
     *
     * @param int $employeeId
     *
     * @return EmployeeFine
     */
    public function setEmployeeId($employeeId)
    {
        $this->employeeId = $employeeId;

        return $this;
    }

    /**
     * Get employeeId.
     *
     * @return int
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * Set type.
     *
     * @param string|null $type
     *
     * @return EmployeeFine
     */
    public function setType($type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set date.
     *
     * @param \DateTime|null $date
     *
     * @return EmployeeFine
     */
    public function setDate($date = null)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime|null
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set time.
     *
     * @param \DateTime|null $time
     *
     * @return EmployeeFine
     */
    public function setTime($time = null)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time.
     *
     * @return \DateTime|null
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set amount.
     *
     * @param int|null $amount
     *
     * @return EmployeeFine
     */
    public function setAmount($amount = null)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount.
     *
     * @return int|null
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set cause.
     *
     * @param string|null $cause
     *
     * @return EmployeeFine
     */
    public function setCause($cause = null)
    {
        $this->cause = $cause;

        return $this;
    }

    /**
     * Get cause.
     *
     * @return string|null
     */
    public function getCause()
    {
        return $this->cause;
    }

    /**
     * Set autoCause.
     *
     * @param string|null $autoCause
     *
     * @return EmployeeFine
     */
    public function setAutoCause($autoCause = null)
    {
        $this->autoCause = $autoCause;

        return $this;
    }

    /**
     * Get autoCause.
     *
     * @return string|null
     */
    public function getAutoCause()
    {
        return $this->autoCause;
    }

    /**
     * Set isHidden.
     *
     * @param bool|null $isHidden
     *
     * @return EmployeeFine
     */
    public function setIsHidden($isHidden = null)
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * Get isHidden.
     *
     * @return bool|null
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return EmployeeFine
     */
    public function setStatus($status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set statusChangedBy.
     *
     * @param int|null $statusChangedBy
     *
     * @return EmployeeFine
     */
    public function setStatusChangedBy($statusChangedBy = null)
    {
        $this->statusChangedBy = $statusChangedBy;

        return $this;
    }

    /**
     * Get statusChangedBy.
     *
     * @return int|null
     */
    public function getStatusChangedBy()
    {
        return $this->statusChangedBy;
    }

    /**
     * Set statusChangedAt.
     *
     * @param \DateTime|null $statusChangedAt
     *
     * @return EmployeeFine
     */
    public function setStatusChangedAt($statusChangedAt = null)
    {
        $this->statusChangedAt = $statusChangedAt;

        return $this;
    }

    /**
     * Get statusChangedAt.
     *
     * @return \DateTime|null
     */
    public function getStatusChangedAt()
    {
        return $this->statusChangedAt;
    }

    /**
     * Set approvedBy.
     *
     * @param int|null $approvedBy
     *
     * @return EmployeeFine
     */
    public function setApprovedBy($approvedBy = null)
    {
        $this->approvedBy = $approvedBy;

        return $this;
    }

    /**
     * Get approvedBy.
     *
     * @return int|null
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * Set approvedAt.
     *
     * @param \DateTime|null $approvedAt
     *
     * @return EmployeeFine
     */
    public function setApprovedAt($approvedAt = null)
    {
        $this->approvedAt = $approvedAt;

        return $this;
    }

    /**
     * Get approvedAt.
     *
     * @return \DateTime|null
     */
    public function getApprovedAt()
    {
        return $this->approvedAt;
    }

    /**
     * Set cancelRequest.
     *
     * @param EmployeeCancelRequest|null $cancelRequest
     *
     * @return EmployeeFine
     */
    public function setCancelRequest($cancelRequest = null)
    {
        $this->cancelRequest = $cancelRequest;

        return $this;
    }

    /**
     * Get cancelRequest.
     *
     * @return EmployeeCancelRequest|null
     */
    public function getCancelRequest()
    {
        return $this->cancelRequest;
    }
}
