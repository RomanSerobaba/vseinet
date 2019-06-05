<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Complaint.
 *
 * @ORM\Table(name="complaint")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ComplaintRepository")
 */
class Complaint
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
     * @var int
     *
     * @ORM\Column(name="comuser_id", type="integer")
     */
    private $comuserId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="manager_name", type="string")
     */
    private $managerName;

    /**
     * @var string
     *
     * @ORM\Column(name="manager_phone", type="string")
     */
    private $managerPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string")
     */
    private $text;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_checked", type="boolean")
     */
    private $isChecked;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->isChecked = false;
    }

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
     * @return Complaint
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
     * Set comuserId.
     *
     * @param int $comuserId
     *
     * @return Complaint
     */
    public function setComuserId($comuserId)
    {
        $this->comuserId = $comuserId;

        return $this;
    }

    /**
     * Get comuserId.
     *
     * @return int
     */
    public function getComuserId()
    {
        return $this->comuserId;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Complaint
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
     * Set type.
     *
     * @param string $type
     *
     * @return Complaint
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set managerName.
     *
     * @param string $managerName
     *
     * @return Complaint
     */
    public function setManagerName($managerName)
    {
        $this->managerName = $managerName;

        return $this;
    }

    /**
     * Get managerName.
     *
     * @return string
     */
    public function getManagerName()
    {
        return $this->managerName;
    }

    /**
     * Set managerPhone.
     *
     * @param string $managerPhone
     *
     * @return Complaint
     */
    public function setManagerPhone($managerPhone)
    {
        $this->managerPhone = $managerPhone;

        return $this;
    }

    /**
     * Get managerPhone.
     *
     * @return string
     */
    public function getManagerPhone()
    {
        return $this->managerPhone;
    }

    /**
     * Set text.
     *
     * @param string $text
     *
     * @return Complaint
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set isChecked.
     *
     * @param bool $isChecked
     *
     * @return Complaint
     */
    public function setIsChecked($isChecked)
    {
        $this->isChecked = $isChecked;

        return $this;
    }

    /**
     * Get isChecked.
     *
     * @return bool
     */
    public function getIsChecked()
    {
        return $this->isChecked;
    }
}
