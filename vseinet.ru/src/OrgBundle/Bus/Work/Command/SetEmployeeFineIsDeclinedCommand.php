<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class SetEmployeeFineIsDeclinedCommand extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     */
    public $id;

    /**
     * @Assert\Type(type="boolean")
     */
    public $value;
}