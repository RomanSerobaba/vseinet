<?php 

namespace SupplyBundle\Bus\Suppliers\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class OneC
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
     * @Assert\Type(type="boolean")
     */
    public $isActive;

    /**
     * Supplier constructor.
     *
     * @param $id
     * @param $name
     * @param $managerId
     * @param $quantity
     */
    public function __construct($id, $name, $managerId, $quantity)
    {
    }
}