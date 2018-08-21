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
    public $blocks;


    public function __construct($representativeId, ...$times)
    {
        $this->representativeId = $representativeId;

        $days = ['пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс']; 
        $blocks = [];
        $period = 0;
        $current = '';
        foreach ($days as $i => $day) {
            $time = $times[$i*2] ? 'с '.$times[$i*2]->format("G:i").' до '.$times[$i*2 + 1]->format("G:i") : 'выходной';
            if ($time !== $current) {
                $current = $time;
                $period += 1;
            }
            $blocks[$period]['days'][] = $day;
            $blocks[$period]['time'] = $time;
        }
        $day = $days[date('N')];
        foreach ($blocks as $block) {
            $current = in_array($day, $block['days']);
            $period = array_shift($block['days']);
            if (count($block['days'])) {
                $period .= '-'.array_pop($block['days']);
            }  
            $this->blocks[] = new ScheduleBlock($period, $block['time'], $current);
        }
    }
}
