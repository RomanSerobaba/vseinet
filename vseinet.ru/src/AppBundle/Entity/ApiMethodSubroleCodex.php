<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApiMethodSubroleCodex
 *
 * @ORM\Table(name="api_method_acl_subrole_codex")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ApiMethodSubroleCodexRepository")
 */
class ApiMethodSubroleCodex
{
    /**
     * @var int
     *
     * @ORM\Column(name="acl_subrole_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $subroleId;

    /**
     * @var int
     *
     * @ORM\Column(name="api_method_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $apiMethodId;


    /**
     * Set subroleId.
     *
     * @param int $subroleId
     *
     * @return ApiMethodSubroleCodex
     */
    public function setSubroleId($subroleId)
    {
        $this->subroleId = $subroleId;

        return $this;
    }

    /**
     * Get subroleId.
     *
     * @return int
     */
    public function getSubroleId()
    {
        return $this->subroleId;
    }

    /**
     * Set apiMethodId.
     *
     * @param int $apiMethodId
     *
     * @return ApiMethodSubroleCodex
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
}
