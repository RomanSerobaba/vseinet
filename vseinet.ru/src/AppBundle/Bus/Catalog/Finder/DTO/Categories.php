<?php

namespace AppBundle\Bus\Catalog\Finder\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Categories
{
    /**
     * @Assert\All(@Assert\Type(type="AppBundle\Bus\Catalog\Finder\DTO\Category"))
     */
    public $id;

    /**
     * @Assert\All(@Assert\Type(type="AppBundle\Bus\Catalog\Finder\DTO\Category"))
     */
    public $tree;

    public function __construct($main = [], $tree = [])
    {
        $this->main = $main;
        $this->tree = $tree;
    }
}
