<?php 

namespace ContentBundle\Bus\Naming\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Item
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
    public $delimiterBefore;

    /**
     * @Assert\Type(type="string")
     */
    public $delimiterAfter;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isRequired;

    /**
     * @Assert\Type(type="integer")
     */
    public $sortOrder;


    public function __construct($id, $name, $delimiterBefore, $delimiterAfter, $isRequired, $sortOrder)
    {
        $this->id = $id;
        $this->name = $name;
        $this->delimiterBefore = $delimiterBefore;
        $this->delimiterAfter = $delimiterAfter;
        $this->isRequired = $isRequired;
        $this->sortOrder = $sortOrder;
    }
}