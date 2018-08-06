<?php

namespace SupplyBundle\Bus\Orders\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Managers
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $fullname;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $pid;

    /**
     * Managers constructor.
     *
     * @param $id
     * @param $fullname
     * @param $name
     * @param $pid
     */
    public function __construct($id, $fullname, $name, $pid)
    {
        $this->id = $id;
        $this->code = $fullname;
        $this->name = $name;
        $this->pid = $pid;
    }
}