<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class AddEmployeeFineCommand extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $cause;

    /**
     * @Assert\Type(type="string")
     * @Assert\Date()
     */
    public $date;

    /**
     * @Assert\Type(type="integer")
     */
    public $amount;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}