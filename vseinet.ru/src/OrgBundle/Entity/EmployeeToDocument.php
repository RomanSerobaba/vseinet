<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeToDocument
 *
 * @ORM\Table(name="org_employee_to_document")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\EmployeeToDocumentRepository")
 */
class EmployeeToDocument
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
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="org_employee_document_id", type="integer")
     */
    private $documentId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
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
     * @ORM\Column(name="checked_at", type="datetime", nullable=true)
     */
    private $checkedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="checked_by", type="integer", nullable=true)
     */
    private $checkedBy;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="due_date", type="date", nullable=true)
     */
    private $dueDate;


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
     * @return EmployeeToDocument
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
     * Set documentId.
     *
     * @param int $documentId
     *
     * @return EmployeeToDocument
     */
    public function setDocumentId($documentId)
    {
        $this->documentId = $documentId;

        return $this;
    }

    /**
     * Get documentId.
     *
     * @return int
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * Set comment.
     *
     * @param string|null $comment
     *
     * @return EmployeeToDocument
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
     * @return EmployeeToDocument
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
     * @return EmployeeToDocument
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
     * Set checkedAt.
     *
     * @param \DateTime|null $checkedAt
     *
     * @return EmployeeToDocument
     */
    public function setCheckedAt($checkedAt = null)
    {
        $this->checkedAt = $checkedAt;

        return $this;
    }

    /**
     * Get checkedAt.
     *
     * @return \DateTime|null
     */
    public function getCheckedAt()
    {
        return $this->checkedAt;
    }

    /**
     * Set checkedBy.
     *
     * @param int|null $checkedBy
     *
     * @return EmployeeToDocument
     */
    public function setCheckedBy($checkedBy = null)
    {
        $this->checkedBy = $checkedBy;

        return $this;
    }

    /**
     * Get checkedBy.
     *
     * @return int|null
     */
    public function getCheckedBy()
    {
        return $this->checkedBy;
    }

    /**
     * Set dueDate.
     *
     * @param \DateTime|null $dueDate
     *
     * @return EmployeeToDocument
     */
    public function setDueDate($dueDate = null)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get dueDate.
     *
     * @return \DateTime|null
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }
}
