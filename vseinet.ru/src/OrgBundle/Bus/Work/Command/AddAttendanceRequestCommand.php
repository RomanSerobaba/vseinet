<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class AddAttendanceRequestCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение employee id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Choice({"absence", "unworking", "overtime"})
     * @Assert\NotBlank(message="Не указан тип корректировки рабочего времени")
     */
    public $type;

    /**
     * @Assert\Type(type="string")
     * @Assert\Date
     */
    public $date;

    /**
     * @Assert\Type(type="string")
     * @Assert\Time
     * @Assert\NotBlank(message="Не указана величина изменения времени")
     */
    public $time;

    /**
     * @Assert\Type(type="string")
     */
    public $cause;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}