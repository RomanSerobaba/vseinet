<?php 

namespace ContentBundle\Bus\Parser\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class DetailValue
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $value;


    public function __construct($id, $value)
    {
        $this->id = $id;
        $this->value = $value;
    }
}