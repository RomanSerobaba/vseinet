<?php

namespace AppBundle\Bus\Product\Query\DTO;

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
    public $sefUrl;


    public function __construct($id, $name, $sefUrl = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sefUrl = $sefUrl;
    }
}
