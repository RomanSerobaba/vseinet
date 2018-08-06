<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResourceUserCodex
 *
 * @ORM\Table(name="resource_user_codex")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResourceUserCodexRepository")
 */
class ResourceUserCodex
{
    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="resource_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $resourceId;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_allowed", type="boolean")
     */
    private $isAllowed;


    /**
     * Set userId.
     *
     * @param int $userId
     *
     * @return ResourceUserCodex
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
     * Set resourceId.
     *
     * @param int $resourceId
     *
     * @return ResourceUserCodex
     */
    public function setResourceId($resourceId)
    {
        $this->resourceId = $resourceId;

        return $this;
    }

    /**
     * Get resourceId.
     *
     * @return int
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * Set isAllowed.
     *
     * @param bool $isAllowed
     *
     * @return ResourceUserCodex
     */
    public function setIsAllowed($isAllowed)
    {
        $this->isAllowed = $isAllowed;

        return $this;
    }

    /**
     * Get isAllowed.
     *
     * @return bool
     */
    public function getIsAllowed()
    {
        return $this->isAllowed;
    }
}
