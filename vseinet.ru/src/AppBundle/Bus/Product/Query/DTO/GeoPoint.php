<?php

namespace AppBundle\Bus\Product\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GeoPoint
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @Assert\Type(type="string")
     */
    public $address;

    public function __construct($id, $code, $name, $quantity, $address = '')
    {
        $this->id = $id;
        $this->code = $code;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->address = $address;
    }
}
