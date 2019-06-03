<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CheaperRequest.
 *
 * @ORM\Table(name="cheaper_request")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CheaperRequestRepository")
 */
class CheaperRequest
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
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;

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
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer")
     */
    private $geoCityId;

    /**
     * @var int
     *
     * @ORM\Column(name="competitor_price", type="integer")
     */
    private $competitorPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="competitor_link", type="string")
     */
    private $competitorLink;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    public function __constructor()
    {
        $this->createdAt = new \DateTime();
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
     * Set baseProductId.
     *
     * @param int $baseProductId
     *
     * @return CheaperRequest
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
     * Set userId.
     *
     * @param int $userId
     *
     * @return CheaperRequest
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
     * @return CheaperRequest
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
     * Set geoCityId.
     *
     * @param int $geoCityId
     *
     * @return CheaperRequest
     */
    public function setGeoCityId($geoCityId)
    {
        $this->geoCityId = $geoCityId;

        return $this;
    }

    /**
     * Get geoCityId.
     *
     * @return int
     */
    public function getGeoCityId()
    {
        return $this->geoCityId;
    }

    /**
     * Set competitorPrice.
     *
     * @param int $competitorPrice
     *
     * @return CheaperRequest
     */
    public function setCompetitorPrice($competitorPrice)
    {
        $this->competitorPrice = $competitorPrice;

        return $this;
    }

    /**
     * Get competitorPrice.
     *
     * @return int
     */
    public function getCompetitorPrice()
    {
        return $this->competitorPrice;
    }

    /**
     * Set competitorLink.
     *
     * @param string $competitorLink
     *
     * @return CheaperRequest
     */
    public function setCompetitorLink($competitorLink)
    {
        $this->competitorLink = $competitorLink;

        return $this;
    }

    /**
     * Get competitorLink.
     *
     * @return string
     */
    public function getCompetitorLink()
    {
        return $this->competitorLink;
    }

    /**
     * Set comment.
     *
     * @param string $comment
     *
     * @return CheaperRequest
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string
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
     * @return CheaperRequest
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
}
