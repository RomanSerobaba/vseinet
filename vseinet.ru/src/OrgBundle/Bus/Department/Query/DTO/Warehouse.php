<?php

namespace OrgBundle\Bus\Department\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Warehouse
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
    public $type;

    /**
     * Warehouse constructor.
     * @param $id
     * @param $name
     * @param $type
     */
    public function __construct($id, $name, $type)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
    }
}