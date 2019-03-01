<?php

namespace AppBundle\Bus\Catalog\Finder\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CategorySection
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
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $detailIds = [];


    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
