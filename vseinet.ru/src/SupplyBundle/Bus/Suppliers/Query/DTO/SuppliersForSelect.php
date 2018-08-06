<?php 

namespace SupplyBundle\Bus\Suppliers\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SuppliersForSelect
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
     * SuppliersForSelect constructor.
     * @param $id
     * @param $code
     */
    public function __construct($id, $code)
    {
        $this->id = $id;
        $this->code = $code;
    }
}