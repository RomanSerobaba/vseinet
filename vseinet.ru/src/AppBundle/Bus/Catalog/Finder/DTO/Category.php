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
     * @Assert\Type(type="string")
     */
    public $parentName;

    /**
     * @Assert\Type(type="integer")
     */
    public $id2;

    /**
     * @Assert\Type(type="string")
     */
    public $name2;

    /**
     * @Assert\Type(type="integer")
     */
    public $id1;

    /**
     * @Assert\Type(type="string")
     */
    public $name1;

    /**
     * @Assert\Type(type="integer")
     */
    public $countProducts;

    /**
     * @Assert\All(@Assert\Type(type="AppBundle\Bus\Catalog\Finder\DTO\Category"))
     */
    public $children;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isActive = false;

    /**
     * @Assert\Type(type="string")
     */
    public $url;

    public function __construct($id, $name, $parentName = null, $id2 = null, $name2 = null, $id1 = null, $name1 = null, $countProducts = 0, $children = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->parentName = $parentName;
        $this->id2 = $id2;
        $this->name2 = $name2;
        $this->id1 = $id1;
        $this->name1 = $name1;
        $this->countProducts = $countProducts;
        $this->children = $children;
    }
}
