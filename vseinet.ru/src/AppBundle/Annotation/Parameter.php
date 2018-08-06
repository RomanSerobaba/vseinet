<?php 

namespace AppBundle\Annotation;

/**
 * @Annotation
 */
class Parameter 
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $in;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $model;

    /**
     * @var boolean
     */
    public $required;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $default;
}