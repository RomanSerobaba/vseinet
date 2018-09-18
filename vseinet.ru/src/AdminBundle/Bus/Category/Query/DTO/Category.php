<?php 

namespace AdminBundle\Bus\Category\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Category 
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
     * @Assert\Type(type="integer")
     */
    public $pid;

    /**
     * @Assert\Type(type="integer")
     */
    public $level;


    public function __construct($id, $name, $pid, $level)
    {
        $this->id = $id;
        $this->name = $name;
        $this->pid = $pid;
        $this->level = $level;
    }
}
