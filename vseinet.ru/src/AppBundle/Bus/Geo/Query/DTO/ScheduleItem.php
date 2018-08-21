<?php

namespace AppBundle\Bus\Geo\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ScheduleItem
{
    /**
     * @Assert\Type(type="string")
     */
    public $count;

    /**
     * @Assert\Type(type="string")
     */
    public $time;


    public function __construct($count, $time)
    {
        $this->count = $count;
        $this->time = $time;
    }
}
 