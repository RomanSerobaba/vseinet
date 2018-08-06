<?php 

namespace ContentBundle\Bus\CategorySection\Query\DTO;

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

    /**
     * @Assert\Type(type="string")
     */
    public $typeCode;

    /**
     * @Assert\Type(type="boolena")
     */
    public $isRequired;


    public function __construct($id, $pid, $name, $typeCode, $isRequired)
    {
        $this->id = $id;
        $this->pid = $pid;
        $this->name = $name;
        $this->typeCode = $typeCode;
        $this->isRequired = $isRequired ? true : false;
    }
}