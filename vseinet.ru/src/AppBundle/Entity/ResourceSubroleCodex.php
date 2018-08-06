<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResourceSubroleCodex
 *
 * @ORM\Table(name="resource_acl_subrole_codex")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResourceSubroleCodexRepository")
 */
class ResourceSubroleCodex
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
     * @ORM\Column(name="resource_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $resourceId;


    /**
     * Set subroleId.
     *
     * @param int $subroleId
     *
     * @return ResourceSubroleCodex
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
     * Set resourceId.
     *
     * @param int $resourceId
     *
     * @return ResourceSubroleCodex
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
}
