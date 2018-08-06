<?php 

namespace SupplyBundle\Bus\Suppliers\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CounteragentsForSupply
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
     * CounteragentsForSupply constructor.
     * @param $id
     * @param $name
     */
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}