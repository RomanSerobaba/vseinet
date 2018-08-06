<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApiMethod
 *
 * @ORM\Table(name="api_method")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ApiMethodRepository")
 */
class ApiMethod
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
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var int
     * 
     * @ORM\Column(name="api_method_section_id", type="integer")
     */
    private $sectionId;

    /**
     * @var string
     *
     * @ORM\Column(name="method", type="string")
     */
    private $method;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string")
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="parameters", type="text")
     */
    private $parameters;

    /**
     * @var string
     *
     * @ORM\Column(name="responses", type="text")
     */
    private $responses;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var int
     * 
     * @ORM\Column(name="sort_order", type="integer")
     */
    private $sortOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="access_right", type="string")
     */
    private $accessRight;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;


    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return ApiMethod
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set sectionId.
     *
     * @param int $sectionId
     *
     * @return ApiMethod
     */
    public function setSectionId($sectionId)
    {
        $this->sectionId = $sectionId;

        return $this;
    }

    /**
     * Get sectionId.
     *
     * @return integer
     */
    public function getSectionId()
    {
        return $this->sectionId;
    }

    /**
     * Set method.
     *
     * @param string $method
     *
     * @return ApiMethod
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set path.
     *
     * @param string $path
     *
     * @return ApiMethod
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set parameters.
     *
     * @param string $parameters
     *
     * @return ApiMethod
     */
    public function setParameters($parameters)
    {
        if (empty($parameters)) {
            $this->parameters = null;
        }
        else {
            $this->parameters = json_encode($parameters);
        }

        return $this;
    }

    /**
     * Get parameters.
     *
     * @return string
     */
    public function getParameters()
    {
        return null === $this->parameters ? null : json_decode($this->parameters, true);
    }

    /**
     * Set responses.
     *
     * @param string $responses
     *
     * @return ApiMethod
     */
    public function setResponses($responses)
    {
        if (empty($responses)) {
            $this->responses = null;
        }
        else {
            $this->responses = json_encode($responses);
        }

        return $this;
    }

    /**
     * Get responses.
     *
     * @return string
     */
    public function getResponses()
    {
        return null === $this->responses ? null : json_decode($this->responses, true);
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return ApiMethod
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set sortOrder.
     *
     * @param int $sortOrder
     *
     * @return ApiMethod
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder.
     *
     * @return integer
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Set accessRight.
     *
     * @param string $accessRight
     *
     * @return ApiMethod
     */
    public function setAccessRight($accessRight)
    {
        $this->accessRight = $accessRight;

        return $this;
    }

    /**
     * Get accessRight.
     *
     * @return string
     */
    public function getAccessRight()
    {
        return $this->accessRight;
    }

    /**
     * Set createdAt.
     *
     * @param string $createdAt
     *
     * @return ApiMethod
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
