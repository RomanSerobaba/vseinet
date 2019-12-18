<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompetitorProduct.
 *
 * @ORM\Table(name="competitor_product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompetitorProductRepository")
 */
class CompetitorProduct
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
     * @ORM\Column(name="competitor_id", type="integer")
     */
    private $competitorId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="competitor_category_id", type="integer", nullable=true)
     */
    private $competitorCategoryId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer")
     */
    private $geoCityId;

    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="url", type="string", nullable=true)
     */
    private $url;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     */
    private $price;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_manual_request", type="boolean")
     */
    private $isManualRequest;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="requested_at", type="datetime", nullable=true)
     */
    private $requestedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="completed_at", type="datetime", nullable=true)
     */
    private $completedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string")
     */
    private $code;

    /**
     * Set defaults.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->isManualRequest = false;
        $this->status = 0;
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
     * Set competitorId.
     *
     * @param int $competitorId
     *
     * @return CompetitorProduct
     */
    public function setCompetitorId($competitorId)
    {
        $this->competitorId = $competitorId;

        return $this;
    }

    /**
     * Get competitorId.
     *
     * @return int
     */
    public function getCompetitorId()
    {
        return $this->competitorId;
    }

    /**
     * Set competitorCategoryId.
     *
     * @param int|null $competitorCategoryId
     *
     * @return CompetitorProduct
     */
    public function setCompetitorCategoryId($competitorCategoryId = null)
    {
        $this->competitorCategoryId = $competitorCategoryId;

        return $this;
    }

    /**
     * Get competitorCategoryId.
     *
     * @return int|null
     */
    public function getCompetitorCategoryId()
    {
        return $this->competitorCategoryId;
    }

    /**
     * Set geoCityId.
     *
     * @param int $geoCityId
     *
     * @return CompetitorProduct
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
     * Set baseProductId.
     *
     * @param int $baseProductId
     *
     * @return CompetitorProduct
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
     * Set name.
     *
     * @param string|null $name
     *
     * @return CompetitorProduct
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
     * Set url.
     *
     * @param string|null $url
     *
     * @return CompetitorProduct
     */
    public function setUrl($url = null)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set price.
     *
     * @param int $price
     *
     * @return CompetitorProduct
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set isManualRequest.
     *
     * @param bool $isManualRequest
     *
     * @return CompetitorProduct
     */
    public function setIsManualRequest($isManualRequest)
    {
        $this->isManualRequest = $isManualRequest;

        return $this;
    }

    /**
     * Get isManualRequest.
     *
     * @return bool
     */
    public function getIsManualRequest()
    {
        return $this->isManualRequest;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime|null $createdAt
     *
     * @return CompetitorProduct
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
     * Set createdBy.
     *
     * @param int|null $createdBy
     *
     * @return CompetitorProduct
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
     * Set status.
     *
     * @param int $status
     *
     * @return CompetitorProduct
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set requestedAt.
     *
     * @param \DateTime|null $requestedAt
     *
     * @return CompetitorProduct
     */
    public function setRequestedAt($requestedAt = null)
    {
        $this->requestedAt = $requestedAt;

        return $this;
    }

    /**
     * Get requestedAt.
     *
     * @return \DateTime|null
     */
    public function getRequestedAt()
    {
        return $this->requestedAt;
    }

    /**
     * Set completedAt.
     *
     * @param \DateTime|null $completedAt
     *
     * @return CompetitorProduct
     */
    public function setCompletedAt($completedAt = null)
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * Get completedAt.
     *
     * @return \DateTime|null
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return CompetitorProduct
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
