<?php

namespace AppBundle\Annotation;

use Symfony\Component\Routing\Annotation\Route as BaseRoute;

/**
 * Route annotation class.
 *
 * @Annotation
 */
class Route extends BaseRoute
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var array
     */
    protected $parameters;
 
    /**
     * @var string
     */
    protected $description;


    public function __construct(array $data)
    {
        if (empty($data['methods'])) {
            $data['methods'] = $this->getMethod();
        }

        parent::__construct($data);
    }
    
    public function getMethod()
    {
        return strtoupper((new \ReflectionClass($this))->getShortName());
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function getParameters()
    {
        return $this->parameters ?: [];
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
