<?php

namespace AppBundle\Bus\Catalog\Finder\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Category
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $countProducts;

    /**
     * @Assert\All(@Assert\Type(type="AppBundle\Bus\Catalog\Finder\DTO\Category"))
     */
    public $children;


    public function __construct($id, $name, $countProducts = 0, $children = []) {
        $this->id = $id;
        $this->name = $name;
        $this->countProducts = $countProducts;
        $this->children = $children;
    }
}
