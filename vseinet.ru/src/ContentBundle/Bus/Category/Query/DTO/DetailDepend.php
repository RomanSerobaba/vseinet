<?php 

namespace ContentBundle\Bus\Category\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class DetailDepend
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $pid;

    /**
     * @Assert\Type(type="string")
     */
    public $name;


    public function __construct($id, $pid, $name)
    {
        $this->id = $id;
        $this->pid = $pid;
        $this->name = $name;
    }
}