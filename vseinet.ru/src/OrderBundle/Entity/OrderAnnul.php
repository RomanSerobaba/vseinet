<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderAnnul
 *
 * @ORM\Table(name="order_annul")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\OrderAnnulRepository")
 */
class OrderAnnul
{
    const ORDER_ANNUL_CAUSE_CODE_OTHER = 'other';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="order_annul_cause_code", type="string", length=255)
     */
    private $orderAnnulCauseCode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="created_by", type="integer")
     */
    private $createdBy;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="approved_at", type="datetime", nullable=true)
     */
    private $approvedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="approved_by", type="integer", nullable=true)
     */
    private $approvedBy;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_reserve_canceled", type="boolean")
     */
    private $isReserveCanceled;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_client_offender", type="boolean")
     */
    private $isClientOffender;


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
     * Set orderAnnulCauseCode.
     *
     * @param string $orderAnnulCauseCode
     *
     * @return OrderAnnul
     */
    public function setOrderAnnulCauseCode($orderAnnulCauseCode)
    {
        $this->orderAnnulCauseCode = $orderAnnulCauseCode;

        return $this;
    }

    /**
     * Get orderAnnulCauseCode.
     *
     * @return string
     */
    public function getOrderAnnulCauseCode()
    {
        return $this->orderAnnulCauseCode;
    }

    /**
     * Set comment.
     *
     * @param string|null $comment
     *
     * @return OrderAnnul
     */
    public function setComment($comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string|null
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return OrderAnnul
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy.
     *
     * @param int $createdBy
     *
     * @return OrderAnnul
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set approvedAt.
     *
     * @param \DateTime|null $approvedAt
     *
     * @return OrderAnnul
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
     * Set approvedBy.
     *
     * @param int|null $approvedBy
     *
     * @return OrderAnnul
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
     * Set isReserveCanceled.
     *
     * @param bool $isReserveCanceled
     *
     * @return OrderAnnul
     */
    public function setIsReserveCanceled($isReserveCanceled)
    {
        $this->isReserveCanceled = $isReserveCanceled;

        return $this;
    }

    /**
     * Get isReserveCanceled.
     *
     * @return bool
     */
    public function getIsReserveCanceled()
    {
        return $this->isReserveCanceled;
    }

    /**
     * Set isClientOffender.
     *
     * @param bool $isClientOffender
     *
     * @return OrderAnnul
     */
    public function setIsClientOffender($isClientOffender)
    {
        $this->isClientOffender = $isClientOffender;

        return $this;
    }

    /**
     * Get isClientOffender.
     *
     * @return bool
     */
    public function getIsClientOffender()
    {
        return $this->isClientOffender;
    }
}
