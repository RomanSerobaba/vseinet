<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ViewHistoryBrand.
 *
 * @ORM\Table(name="view_history_brand")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ViewHistoryBrandRepository")
 */
class ViewHistoryBrand
{
    /**
     * @var int
     *
     * @ORM\Column(name="brand_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $brandId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer")
     */
    private $geoCityId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="viewed_at", type="datetime")
     */
    private $viewedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip;

    /**
     * Set brandId.
     *
     * @param int $brandId
     *
     * @return ViewHistoryBrand
     */
    public function setBrandId($brandId)
    {
        $this->brandId = $brandId;

        return $this;
    }

    /**
     * Get brandId.
     *
     * @return int
     */
    public function getBrandId()
    {
        return $this->brandId;
    }

    /**
     * Set geoCityId.
     *
     * @param int $geoCityId
     *
     * @return ViewHistoryBrand
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
     * Set userId.
     *
     * @param int $userId
     *
     * @return ViewHistoryBrand
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
     * Set viewedAt.
     *
     * @param \DateTime $viewedAt
     *
     * @return ViewHistoryBrand
     */
    public function setViewedAt($viewedAt)
    {
        $this->viewedAt = $viewedAt;

        return $this;
    }

    /**
     * Get viewedAt.
     *
     * @return \DateTime
     */
    public function getViewedAt()
    {
        return $this->viewedAt;
    }

    /**
     * Set ip.
     *
     * @param string $ip
     *
     * @return ViewHistoryBrand
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip.
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }
}
