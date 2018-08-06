<?php

namespace OrgBundle\Bus\Work\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AttendanceRequest
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $type;

    /**
     * @Assert\Type(type="\DateTime")
     */
    public $date;

    /**
     * @Assert\Type(type="\DateTime")
     */
    public $time;

    /**
     * @Assert\Type(type="integer")
     */
    public $amount;

    /**
     * @Assert\Type(type="string")
     */
    public $cause;

    /**
     * @Assert\Type(type="string")
     */
    public $autoCause;

    /**
     * @Assert\Type(type="string")
     */
    public $status;

    /**
     * @Assert\Type(type="string")
     */
    public $statusChangedBy;

    /**
     * @Assert\Type(type="\DateTime")
     */
    public $statusChangedAt;

    /**
     * @Assert\Type(type="string")
     */
    public $approvedBy;

    /**
     * @Assert\Type(type="\DateTime")
     */
    public $approvedAt;

    /**
     * @Assert\Type(type="string")
     */
    public $requestCause;

    /**
     * @Assert\Type(type="string")
     */
    public $requestStatus;

    /**
     * @Assert\Type(type="string")
     */
    public $requestStatusChangedBy;

    /**
     * @Assert\Type(type="\DateTime")
     */
    public $requestStatusChangedAt;

    /**
     * @Assert\Type(type="string")
     */
    public $requestApprovedBy;

    /**
     * @Assert\Type(type="\DateTime")
     */
    public $requestApprovedAt;


    /**
     * AttendanceRequest constructor.
     * @param $id
     * @param $type
     * @param $date
     * @param $time
     * @param $amount
     * @param $cause
     * @param $autoCause
     * @param $status
     * @param $statusChangedBy
     * @param $statusChangedAt
     * @param $approvedBy
     * @param $approvedAt
     * @param $requestCause
     * @param $requestStatus
     * @param $requestStatusChangedBy
     * @param $requestStatusChangedAt
     * @param $requestApprovedBy
     * @param $requestApprovedAt
     */
    public function __construct(
        $id,
        $type,
        $date,
        $time,
        $amount,
        $cause,
        $autoCause,
        $status,
        $statusChangedBy,
        $statusChangedAt,
        $approvedBy,
        $approvedAt,
        $requestCause,
        $requestStatus,
        $requestStatusChangedBy,
        $requestStatusChangedAt,
        $requestApprovedBy,
        $requestApprovedAt
    )
    {
        $this->id = $id;
        $this->type = $type;
        $this->date = $date;
        $this->time = $time;
        $this->amount = $amount;
        $this->cause = $cause;
        $this->autoCause = $autoCause;
        $this->status = $status;
        $this->statusChangedBy = $statusChangedBy;
        $this->statusChangedAt = $statusChangedAt;
        $this->approvedBy = $approvedBy;
        $this->approvedAt = $approvedAt;
        $this->requestCause = $requestCause;
        $this->requestStatus = $requestStatus;
        $this->requestStatusChangedBy = $requestStatusChangedBy;
        $this->requestStatusChangedAt = $requestStatusChangedAt;
        $this->requestApprovedBy = $requestApprovedBy;
        $this->requestApprovedAt = $requestApprovedAt;
    }
}