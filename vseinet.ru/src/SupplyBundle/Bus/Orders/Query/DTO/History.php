<?php

namespace SupplyBundle\Bus\Orders\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class History
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
    public $fullname;

    /**
     * @Assert\Type(type="datetime")
     */
    public $updatedAt;

    /**
     * History constructor.
     *
     * @param $id
     * @param $fullname
     * @param $name
     * @param $updatedAt
     */
    public function __construct($id, $fullname, $name, $updatedAt)
    {
        $this->id = $id;
        $this->fullname = $fullname;
        $this->name = $name;
        $this->updatedAt = $updatedAt;
    }
}