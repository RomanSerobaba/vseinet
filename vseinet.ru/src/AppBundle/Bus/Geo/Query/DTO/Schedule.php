<?php 

namespace AppBundle\Bus\Geo\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Schedule
{
    /**
     * @Assert\Type(type="integer")
     */
    public $representativeId;

    /**
     * @Assert\Type(type="array<AppBundle\Bus\Geo\Query\DTO\ScheduleBlock>")
     */
    public $blocks


    public function($representativeId, ...$times)
    {
        $this->representativeId = $representativeId;

        $days = array('пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс'); 
        $blocks = [];
        $period = 0;
        $current = '';
        foreach ($days as $i => $day) {
            $time = $times[$i * 2] ? 'с '.date("G:i", $times[$i * 2]).' до '.date("G:i", $times[$i * 2 + 1]) : 'выходной';
            if ($time !== $current) {
                $current = $time;
                $period += 1;
            }
            $blocks[$period]['days'][] = $day;
            $blocks[$period]['time'] = $time;
        }
        foreach ($blocks as $block) {
            $period = array_shift($block['days']);
            if (count($block['days'])) {
                $period .= '-'.array_pop($block['days']);
            }  
            $this->blocks[] = new ScheduleBlock($period, $block['time']);
        }
    }
}
