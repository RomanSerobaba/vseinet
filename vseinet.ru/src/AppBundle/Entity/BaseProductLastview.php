<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BaseProductLastview
 *
 * @ORM\Table(name="base_product_lastview")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BaseProductLastviewRepository")
 */
class BaseProductLastview
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="viewed_at", type="datetime")
     */
    private $viewedAt;


    /**
     * Set userId.
     *
     * @param int $userId
     *
     * @return BaseProductLastview
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
     * Set baseProductId.
     *
     * @param int $baseProductId
     *
     * @return BaseProductLastview
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
     * Set viewedAt.
     *
     * @param \DateTime $viewedAt
     *
     * @return BaseProductLastview
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
}
