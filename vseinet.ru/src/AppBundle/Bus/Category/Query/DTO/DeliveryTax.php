<?php 

namespace AppBundle\Bus\Category\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class DeliveryTax 
{
    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $tax;


    public function __construct($name, $tax)
    {
        $this->name = $name;
        $this->tax = $tax;
    }
}
