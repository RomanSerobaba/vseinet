<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class AddEmployeeWageCommand extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     * @Assert\Date()
     */
    public $activeSince;

    /**
     * @Assert\Type(type="string")
     * @Assert\Date()
     */
    public $activeTill;

    /**
     * @Assert\Type(type="integer")
     */
    public $constantBase;

    /**
     * @Assert\Type(type="integer")
     */
    public $planBase;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}