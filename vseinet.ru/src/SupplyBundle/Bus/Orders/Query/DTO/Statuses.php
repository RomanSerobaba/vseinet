<?php

namespace SupplyBundle\Bus\Orders\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Statuses
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
    public $code;

    /**
     * Statuses constructor.
     *
     * @param $id
     * @param $name
     * @param $code
     */
    public function __construct($id, $name, $code)
    {
        $this->id = $id;
        $this->code = $code;
        $this->name = $name;
    }
}