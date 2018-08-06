<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class AddEmployeeScheduleCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение employee id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     * @Assert\Date
     */
    public $activeSince;

    /**
     * @Assert\Type(type="string")
     * @Assert\Date
     */
    public $activeTill;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isIrregular;

    /**
     * @Assert\Type(type="string")
     * @Assert\Time
     */
    public $s1;

    /**
     * @Assert\Type(type="string")
     * @Assert\Time
     */
    public $t1;

    /**
     * @Assert\Type(type="string")
     * @Assert\Time
     */
    public $s2;

    /**
     * @Assert\Type(type="string")
     * @Assert\Time
     */
    public $t2;

    /**
     * @Assert\Type(type="string")
     * @Assert\Time
     */
    public $s3;

    /**
     * @Assert\Type(type="string")
     * @Assert\Time
     */
    public $t3;

    /**
     * @Assert\Type(type="string")
     * @Assert\Time
     */
    public $s4;

    /**
     * @Assert\Type(type="string")
     * @Assert\Time
     */
    public $t4;

    /**
     * @Assert\Type(type="string")
     * @Assert\Time
     */
    public $s5;

    /**
     * @Assert\Type(type="string")
     * @Assert\Time
     */
    public $t5;

    /**
     * @Assert\Type(type="string")
     * @Assert\Time
     */
    public $s6;

    /**
     * @Assert\Type(type="string")
     * @Assert\Time
     */
    public $t6;

    /**
     * @Assert\Type(type="string")
     * @Assert\Time
     */
    public $s7;

    /**
     * @Assert\Type(type="string")
     * @Assert\Time
     */
    public $t7;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}