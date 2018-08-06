<?php 

namespace ContentBundle\Bus\ColorComposite\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Schema
{
    /**
     * @Assert\Type(type="string")
     */
    public $type;

    /**
     * @Assert\Type(type="string")
     */
    public $name;


    public function __construct($type, $name)
    {
        $this->type = $type;
        $this->name = $name;
    }
}