<?php 

namespace AppBundle\Bus\Geo\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ScheduleBlock
{
    /**
     * @Assert\Type(type="string")
     */
    public $period;

    /**
     * @Assert\Type(type="string")
     */
    public $time;

    /**
     * @Assert\type(type="boolean")
     */
    public $current;


    public function __construct($period, $time, $current)
    {
        $this->period = $period;
        $this->time = $time;
        $this->current = $current;
    }
}
