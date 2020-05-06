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
     * @ORM\Column(name="partner_product_id", type="integer")
     */
    private $partnerProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="competitor_id", type="integer")
     */
    private $competitorId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="base_product_id", type="integer", nullable=true)
     */
    private $baseProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer")
     */
    private $geoCityId;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     */
    private $price;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->price = 0;
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
     * Set partnerProductId.
     *
     * @param int $partnerProductId
     *
     * @return CompetitorProduct
     */
    public function setPartnerProductId($partnerProductId)
    {
        $this->partnerProductId = $partnerProductId;

        return $this;
    }

    /**
     * Get partnerProductId.
     *
     * @return int
     */
    public function getPartnerProductId()
    {
        return $this->partnerProductId;
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
     * Set baseProductId.
     *
     * @param int|null $baseProductId
     *
     * @return CompetitorProduct
     */
    public function setBaseProductId($baseProductId = null)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get baseProductId.
     *
     * @return int|null
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
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
     * Set updatedAt.
     *
     * @param \DateTime|null $updatedAt
     *
     * @return CompetitorProduct
     */
    public function setUpdatedAt($updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
