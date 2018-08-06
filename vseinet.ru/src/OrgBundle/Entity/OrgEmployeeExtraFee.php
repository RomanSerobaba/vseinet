<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrgEmployeeExtraFee
 *
 * @ORM\Table(name="org_employee_extra_fee")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\OrgEmployeeExtraFeeRepository")
 */
class OrgEmployeeExtraFee
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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reason", type="string", length=255, nullable=true)
     */
    private $reason;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var int|null
     *
     * @ORM\Column(name="amount", type="integer", nullable=true)
     */
    private $amount;

    /**
     * @var int|null
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

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
     * @var int|null
     *
     * @ORM\Column(name="applied_by", type="integer", nullable=true)
     */
    private $appliedBy;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="applied_at", type="datetime", nullable=true)
     */
    private $appliedAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="extra_fee_type", type="string", length=255, nullable=true)
     */
    private $extraFeeType;

    /**
     * @var int|null
     *
     * @ORM\Column(name="declined_by", type="integer", nullable=true)
     */
    private $declinedBy;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="declined_at", type="datetime", nullable=true)
     */
    private $declinedAt;


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
     * Set userId.
     *
     * @param int $userId
     *
     * @return OrgEmployeeExtraFee
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set reason.
     *
     * @param string|null $reason
     *
     * @return OrgEmployeeExtraFee
     */
    public function setReason($reason = null)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason.
     *
     * @return string|null
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set date.
     *
     * @param \DateTime|null $date
     *
     * @return OrgEmployeeExtraFee
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
     * Set amount.
     *
     * @param int|null $amount
     *
     * @return OrgEmployeeExtraFee
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
     * Set createdBy.
     *
     * @param int|null $createdBy
     *
     * @return OrgEmployeeExtraFee
     */
    public function setCreatedBy($createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return int|null
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime|null $createdAt
     *
     * @return OrgEmployeeExtraFee
     */
    public function setCreatedAt($createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set approvedBy.
     *
     * @param int|null $approvedBy
     *
     * @return OrgEmployeeExtraFee
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
     * @return OrgEmployeeExtraFee
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
     * Set appliedBy.
     *
     * @param int|null $appliedBy
     *
     * @return OrgEmployeeExtraFee
     */
    public function setAppliedBy($appliedBy = null)
    {
        $this->appliedBy = $appliedBy;

        return $this;
    }

    /**
     * Get appliedBy.
     *
     * @return int|null
     */
    public function getAppliedBy()
    {
        return $this->appliedBy;
    }

    /**
     * Set appliedAt.
     *
     * @param \DateTime|null $appliedAt
     *
     * @return OrgEmployeeExtraFee
     */
    public function setAppliedAt($appliedAt = null)
    {
        $this->appliedAt = $appliedAt;

        return $this;
    }

    /**
     * Get appliedAt.
     *
     * @return \DateTime|null
     */
    public function getAppliedAt()
    {
        return $this->appliedAt;
    }

    /**
     * Set extraFeeType.
     *
     * @param string|null $extraFeeType
     *
     * @return OrgEmployeeExtraFee
     */
    public function setExtraFeeType($extraFeeType = null)
    {
        $this->extraFeeType = $extraFeeType;

        return $this;
    }

    /**
     * Get extraFeeType.
     *
     * @return string|null
     */
    public function getExtraFeeType()
    {
        return $this->extraFeeType;
    }

    /**
     * Set declinedBy.
     *
     * @param int|null $declinedBy
     *
     * @return OrgEmployeeExtraFee
     */
    public function setDeclinedBy($declinedBy = null)
    {
        $this->declinedBy = $declinedBy;

        return $this;
    }

    /**
     * Get declinedBy.
     *
     * @return int|null
     */
    public function getDeclinedBy()
    {
        return $this->declinedBy;
    }

    /**
     * Set declinedAt.
     *
     * @param \DateTime|null $declinedAt
     *
     * @return OrgEmployeeExtraFee
     */
    public function setDeclinedAt($declinedAt = null)
    {
        $this->declinedAt = $declinedAt;

        return $this;
    }

    /**
     * Get declinedAt.
     *
     * @return \DateTime|null
     */
    public function getDeclinedAt()
    {
        return $this->declinedAt;
    }
}
