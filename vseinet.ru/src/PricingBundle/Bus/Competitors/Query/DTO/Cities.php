<?php 

namespace PricingBundle\Bus\Competitors\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Cities
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
     * @Assert\Type(type="integer")
     */
    public $looseCount;

    /**
     * Cities constructor.
     * @param $id
     * @param $name
     * @param $looseCount
     */
    public function __construct($id, $name, $looseCount)
    {
        $this->id = $id;
        $this->name = $name;
        $this->looseCount = $looseCount;
    }
}