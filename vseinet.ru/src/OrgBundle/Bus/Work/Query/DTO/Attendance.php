<?php

namespace OrgBundle\Bus\Work\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Attendance
{
    /**
     * @Assert\Type(type="string")
     */
    public $month;

    /**
     * @Assert\Type(type="array")
     */
    public $days;

    /**
     * @Assert\Type(type="array")
     */
    public $schedule;

    /**
     * @Assert\Type(type="array")
     */
    public $summary;

    /**
     * @Assert\Type(type="boolean")
     */
    public $areYouChief;

    /**
     * @Assert\Type(type="boolean")
     */
    public $areYouAdmin;
}