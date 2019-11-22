<?php

namespace AppBundle\Bus\Catalog\Finder\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Brand
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
    public $rating;

    /**
     * @Assert\Type(type="integer")
     */
    public $countProducts;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isTop;

    /**
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $includeIds;

    public function __construct($id, $name, $rating)
    {
        $this->id = $id;
        $this->name = $name;
        $this->rating = $rating;
    }
}
