<?php

namespace SupplyBundle\Bus\Orders\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Suppliers
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
    public $fullname;
}