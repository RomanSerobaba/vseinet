<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientSuggestion
 *
 * @ORM\Table(name="client_suggestion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientSuggestionRepository")
 */
class ClientSuggestion
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
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_checked", type="boolean")
     */
    private $isChecked;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Suggetion
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
     * Set comuserId
     *
     * @param integer $comuserId
     *
     * @return Suggetion
     */
    public function setComuserId($comuserId)
    {
        $this->comuserId = $comuserId;

        return $this;
    }

    /**
     * Get comuserId
     *
     * @return int
     */
    public function getComuserId()
    {
        return $this->comuserId;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Suggetion
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Suggetion
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set isChecked
     *
     * @param boolean $isChecked
     *
     * @return Suggetion
     */
    public function setIsChecked($isChecked)
    {
        $this->isChecked = $isChecked;

        return $this;
    }

    /**
     * Get isChecked
     *
     * @return bool
     */
    public function getIsChecked()
    {
        return $this->isChecked;
    }
}

