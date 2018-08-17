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


    public function __construct($id, $code, $name, $quantity)
    {
        $this->id = $id;
        $this->code = $code;
        $this->name = $name;
        $this->quantity = $quantity;      
    }
}
