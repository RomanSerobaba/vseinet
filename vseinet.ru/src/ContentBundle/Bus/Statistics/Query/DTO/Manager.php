<?php 

namespace ContentBundle\Bus\Statistics\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Manager
{    
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;


    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}