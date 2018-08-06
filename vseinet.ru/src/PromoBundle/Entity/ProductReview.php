<?php

namespace PromoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductReview
 *
 * @ORM\Table(name="product_review")
 * @ORM\Entity(repositoryClass="PromoBundle\Repository\ProductReviewRepository")
 */
class ProductReview
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
     * @var int|null
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="advantages", type="string", length=255, nullable=true)
     */
    private $advantages;

    /**
     * @var string|null
     *
     * @ORM\Column(name="disadvantages", type="string", length=255, nullable=true)
     */
    private $disadvantages;

    /**
     * @var int
     *
     * @ORM\Column(name="estimate", type="integer")
     */
    private $estimate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=150, nullable=true)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;

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
     * @var \DateTime|null
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="deleted_by", type="integer", nullable=true)
     */
    private $deletedBy;

    /**
     * @var string|null
     *
     * @ORM\Column(name="contacts", type="string", length=255, nullable=true)
     */
    private $contacts;

    /**
     * @var string|null
     *
     * @ORM\Column(name="answer", type="string", length=255, nullable=true)
     */
    private $answer;


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
     * Set createdBy.
     *
     * @param int|null $createdBy
     *
     * @return ProductReview
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
     * @param \DateTime $createdAt
     *
     * @return ProductReview
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
     * Set comment.
     *
     * @param string|null $comment
     *
     * @return ProductReview
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
     * Set advantages.
     *
     * @param string|null $advantages
     *
     * @return ProductReview
     */
    public function setAdvantages($advantages = null)
    {
        $this->advantages = $advantages;

        return $this;
    }

    /**
     * Get advantages.
     *
     * @return string|null
     */
    public function getAdvantages()
    {
        return $this->advantages;
    }

    /**
     * Set disadvantages.
     *
     * @param string|null $disadvantages
     *
     * @return ProductReview
     */
    public function setDisadvantages($disadvantages = null)
    {
        $this->disadvantages = $disadvantages;

        return $this;
    }

    /**
     * Get disadvantages.
     *
     * @return string|null
     */
    public function getDisadvantages()
    {
        return $this->disadvantages;
    }

    /**
     * Set estimate.
     *
     * @param int $estimate
     *
     * @return ProductReview
     */
    public function setEstimate($estimate)
    {
        $this->estimate = $estimate;

        return $this;
    }

    /**
     * Get estimate.
     *
     * @return int
     */
    public function getEstimate()
    {
        return $this->estimate;
    }

    /**
     * Set name.
     *
     * @param string|null $name
     *
     * @return ProductReview
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set baseProductId.
     *
     * @param int $baseProductId
     *
     * @return ProductReview
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get baseProductId.
     *
     * @return int
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Set approvedAt.
     *
     * @param \DateTime|null $approvedAt
     *
     * @return ProductReview
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
     * @return ProductReview
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
     * Set deletedAt.
     *
     * @param \DateTime|null $deletedAt
     *
     * @return ProductReview
     */
    public function setDeletedAt($deletedAt = null)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt.
     *
     * @return \DateTime|null
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set deletedBy.
     *
     * @param int|null $deletedBy
     *
     * @return ProductReview
     */
    public function setDeletedBy($deletedBy = null)
    {
        $this->deletedBy = $deletedBy;

        return $this;
    }

    /**
     * Get deletedBy.
     *
     * @return int|null
     */
    public function getDeletedBy()
    {
        return $this->deletedBy;
    }

    /**
     * Set contacts.
     *
     * @param string|null $contacts
     *
     * @return ProductReview
     */
    public function setContacts($contacts = null)
    {
        $this->contacts = $contacts;

        return $this;
    }

    /**
     * Get contacts.
     *
     * @return string|null
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Set answer.
     *
     * @param string|null $answer
     *
     * @return ProductReview
     */
    public function setAnswer($answer = null)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer.
     *
     * @return string|null
     */
    public function getAnswer()
    {
        return $this->answer;
    }
}
