<?php 

namespace AppBundle\Annotation;

/**
 * @Annotation
 */
class Property 
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $model;

    /**
     * @var string
     */
    public $description;
}