<?php

namespace PricingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductToCompetitor
 *
 * @ORM\Table(name="product_to_competitor")
 * @ORM\Entity(repositoryClass="PricingBundle\Repository\ProductToCompetitorRepository")
 */
class ProductToCompetitor
{
    const STATUS_ADDED = 'added';
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';

    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $baseProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="competitor_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $competitorId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $cityId;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", nullable=true)
     */
    private $link;

    /**
     * @var int
     *
     * @ORM\Column(name="competitor_price", type="integer")
     */
    private $competitorPrice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="price_time", type="datetime", nullable=true)
     */
    private $priceTime;

    /**
     * @var int
     *
     * @ORM\Column(name="attempt", type="integer")
     */
    private $attempt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="requested_at", type="datetime", nullable=true)
     */
    private $requestedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    private $status;

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
     * @var string
     *
     * @ORM\Column(name="server_response", type="string")
     */
    private $serverResponse;


    /**
     * Set baseProductId
     *
     * @param integer $baseProductId
     *
     * @return ProductToCompetitor
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get baseProductId
     *
     * @return int
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Set competitorId
     *
     * @param integer $competitorId
     *
     * @return ProductToCompetitor
     */
    public function setCompetitorId($competitorId)
    {
        $this->competitorId = $competitorId;

        return $this;
    }

    /**
     * Get competitorId
     *
     * @return int
     */
    public function getCompetitorId()
    {
        return $this->competitorId;
    }

    /**
     * Set cityId
     *
     * @param integer $cityId
     *
     * @return ProductToCompetitor
     */
    public function setCityId($cityId)
    {
        $this->cityId = $cityId;

        return $this;
    }

    /**
     * Get cityId
     *
     * @return int
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * Set link
     *
     * @param string $link
     *
     * @return ProductToCompetitor
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set competitorPrice
     *
     * @param integer $competitorPrice
     *
     * @return ProductToCompetitor
     */
    public function setCompetitorPrice($competitorPrice)
    {
        $this->competitorPrice = $competitorPrice;

        return $this;
    }

    /**
     * Get competitorPrice
     *
     * @return int
     */
    public function getCompetitorPrice()
    {
        return $this->competitorPrice;
    }

    /**
     * Set priceTime
     *
     * @param \DateTime $priceTime
     *
     * @return ProductToCompetitor
     */
    public function setPriceTime($priceTime)
    {
        $this->priceTime = $priceTime;

        return $this;
    }

    /**
     * Get priceTime
     *
     * @return \DateTime
     */
    public function getPriceTime()
    {
        return $this->priceTime;
    }

    /**
     * Set attempt
     *
     * @param integer $attempt
     *
     * @return ProductToCompetitor
     */
    public function setAttempt($attempt)
    {
        $this->attempt = $attempt;

        return $this;
    }

    /**
     * Get attempt
     *
     * @return int
     */
    public function getAttempt()
    {
        return $this->attempt;
    }

    /**
     * Set requestedAt
     *
     * @param \DateTime $requestedAt
     *
     * @return ProductToCompetitor
     */
    public function setRequestedAt($requestedAt)
    {
        $this->requestedAt = $requestedAt;

        return $this;
    }

    /**
     * Get requestedAt
     *
     * @return \DateTime
     */
    public function getRequestedAt()
    {
        return $this->requestedAt;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return ProductToCompetitor
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set server response
     *
     * @param string $serverResponse
     *
     * @return ProductToCompetitor
     */
    public function setServerResponse($serverResponse)
    {
        $this->serverResponse = $serverResponse;

        return $this;
    }

    /**
     * Get server response
     *
     * @return string
     */
    public function getServerResponse()
    {
        return $this->serverResponse;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ProductToCompetitor
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
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return ProductToCompetitor
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}

