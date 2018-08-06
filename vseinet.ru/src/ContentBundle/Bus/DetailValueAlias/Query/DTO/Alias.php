<?php 

namespace ContentBundle\Bus\DetailValueAlias\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Alias
{    
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $value;

    /**
     * @Assert\Type(type="integer")
     */
    public $valueId;


    public function __construct($id, $value, $valueId)
    {
        $this->id = $id;
        $this->value = $value;
        $this->valueId = $valueId;
    }
}