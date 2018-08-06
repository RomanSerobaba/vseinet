<?php 

namespace SiteBundle\Bus\Representative\Query\DTO;

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


    public function($period, $time)
    {
        $this->period = $period;
        $this->time = $time;
    }
}