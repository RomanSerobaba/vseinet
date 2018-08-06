<?php 

namespace ContentBundle\Bus\Measure\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Measure
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
     * @Assert\Type(type="boolean")
     */
    public $isUsed;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $unitIds = [];


    public function __construct($id, $name, $isUsed) 
    {
        $this->id = $id;
        $this->name = $name;
        $this->isUsed = $isUsed;
    }
}