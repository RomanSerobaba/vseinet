<?php

namespace AppBundle\Bus\Product\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GeoPoint
{
    /**
     * @Assert\Type("integer")
     */
    public $id;

    /**
     * @Assert\Type("integer")
     */
    public $geoCityId;

    /**
     * @Assert\Type("string")
     */
    public $code;

    /**
     * @Assert\Type("string")
     */
    public $name;

    /**
     * @Assert\Type("integer")
     */
    public $quantity;

    /**
     * @Assert\Type("string")
     */
    public $address;

    public function __construct($id, $geoCityId, $code, $name, $quantity = 0, $address = '')
    {
        $this->id = $id;
        $this->geoCiryId = $geoCityId;
        $this->code = $code;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->address = $address;
    }
}
