<?php

namespace AppBundle\Bus\Geo\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class StreetFound
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
    public $unit;


    public function __construct($id, $name, $unit)
    {
        $this->id = $id;
        $this->name = $name;
        $this->unit = $unit;
    }
}
