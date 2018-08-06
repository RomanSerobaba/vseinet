<?php 

namespace AppBundle\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Response 
{
    /**
     * @var integer
     */
    public $status;

    /**
     * @var array
     */
    public $properties;

    /**
     * @var string
     */
    public $description;
}