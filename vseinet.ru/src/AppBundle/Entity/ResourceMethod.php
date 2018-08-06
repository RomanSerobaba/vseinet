<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResourceMethod
 *
 * @ORM\Table(name="resource_method")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResourceMethodRepository")
 */
class ResourceMethod
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
     * @ORM\Column(name="resource_id", type="integer")
     */
    private $resourceId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="api_method_id", type="integer", nullable=true)
     */
    private $apiMethodId;


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
     * Set resourceId.
     *
     * @param int $resourceId
     *
     * @return ResourceMethod
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
     * Set apiMethodId.
     *
     * @param int|null $apiMethodId
     *
     * @return ResourceMethod
     */
    public function setApiMethodId($apiMethodId = null)
    {
        $this->apiMethodId = $apiMethodId;

        return $this;
    }

    /**
     * Get apiMethodId.
     *
     * @return int|null
     */
    public function getApiMethodId()
    {
        return $this->apiMethodId;
    }
}
