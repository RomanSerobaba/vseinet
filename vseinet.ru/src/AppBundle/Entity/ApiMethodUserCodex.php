<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApiMethodUserCodex
 *
 * @ORM\Table(name="api_method_user_codex")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ApiMethodUserCodexRepository")
 */
class ApiMethodUserCodex
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
     * @ORM\Column(name="api_method_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $apiMethodId;

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
     * @return ApiMethodUserCodex
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
     * Set apiMethodId.
     *
     * @param int $apiMethodId
     *
     * @return ApiMethodUserCodex
     */
    public function setApiMethodId($apiMethodId)
    {
        $this->apiMethodId = $apiMethodId;

        return $this;
    }

    /**
     * Get apiMethodId.
     *
     * @return int
     */
    public function getApiMethodId()
    {
        return $this->apiMethodId;
    }

    /**
     * Set isAllowed.
     *
     * @param bool $isAllowed
     *
     * @return ApiMethodUserCodex
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
