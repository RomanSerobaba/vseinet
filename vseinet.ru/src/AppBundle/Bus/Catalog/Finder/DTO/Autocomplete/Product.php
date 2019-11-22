<?php

namespace AppBundle\Bus\Catalog\Finder\DTO\Autocomplete;

use Symfony\Component\Validator\Constraints as Assert;

class Product
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
    public $type = 'product';

    /**
     * @Assert\Type(type="string")
     */
    public $label;


    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }
}
