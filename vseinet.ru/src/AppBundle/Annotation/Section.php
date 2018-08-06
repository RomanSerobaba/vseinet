<?php

namespace AppBundle\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Section
{
    /**
     * @var string
     */
    public $name;
}