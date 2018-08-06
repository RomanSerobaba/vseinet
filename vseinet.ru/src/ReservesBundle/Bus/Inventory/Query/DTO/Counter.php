<?php 

namespace ReservesBundle\Bus\Inventory\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class Counter
{
    /**
     * @Assert\Type(type="integer")
     */
    public $total;

    public function __construct($total)
    {
        $this->total = $total;
    }
}