<?php 

namespace OrderBundle\Bus\Item\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Status
{
    /**
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @Assert\Type(type="string")
     */
    public $name;
    
    /**
     * OrderItemStatus constructor.
     *
     * @param $code
     * @param $name
     */
    public function __construct($code, $name)
    {
        $this->code = $code;
        $this->name = $name;
    }
}