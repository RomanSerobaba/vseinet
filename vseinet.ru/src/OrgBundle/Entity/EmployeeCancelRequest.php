<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeCancelRequest
 *
 * @ORM\Table(name="org_employee_cancel_request")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\EmployeeCancelRequestRepository")
 */
class EmployeeCancelRequest
{
    /**
     * @var int
     *
     * @ORM\Column(name="fine_id", type="integer")
     * @ORM\Id
     */
    private $fineId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cause", type="text", nullable=true)
     */
    private $cause;

    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
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
     * @var EmployeeFine|null
     *
     * @ORM\OneToOne(targetEntity="EmployeeFine", inversedBy="cancelRequest")
     * @ORM\JoinColumn(name="fine_id", referencedColumnName="id")
     */
    private $fine;


    /**
     * Set fineId.
     *
     * @param int $fineId
     *
     * @return EmployeeCancelRequest
     */
    public function setFineId($fineId)
    {
        $this->fineId = $fineId;

        return $this;
    }

    /**
     * Get fineId.
     *
     * @return int
     */
    public function getFineId()
    {
        return $this->fineId;
    }

    /**
     * Set cause.
     *
     * @param string|null $cause
     *
     * @return EmployeeCancelRequest
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
     * Set status.
     *
     * @param string|null $status
     *
     * @return EmployeeCancelRequest
     */
    public function setStatus($status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string|null
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
     * @return EmployeeCancelRequest
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
     * @return EmployeeCancelRequest
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
     * @return EmployeeCancelRequest
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
     * @return EmployeeCancelRequest
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
     * Set fine.
     *
     * @param EmployeeFine|null $fine
     *
     * @return EmployeeCancelRequest
     */
    public function setFine($fine = null)
    {
        $this->fine = $fine;
        $this->fineId = $fine->getId();

        return $this;
    }

    /**
     * Get fine.
     *
     * @return EmployeeFine|null
     */
    public function getFine()
    {
        return $this->fine;
    }
}
