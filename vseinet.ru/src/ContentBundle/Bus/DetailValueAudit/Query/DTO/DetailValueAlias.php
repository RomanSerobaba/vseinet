<?php 

namespace ContentBundle\Bus\DetailValueAudit\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class DetailValueAlias
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