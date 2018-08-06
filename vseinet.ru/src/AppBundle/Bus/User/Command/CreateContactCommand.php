<?php

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;

class CreateContactCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Выберите тип контакта")
     * @Enum("AppBundle\Enum\ContactTypeCode")
     */
    public $typeCode;

    /**
     * @Assert\NotBlank(message="Введите контакт")
     * @Assert\Type(type="string")
     */
    public $value;

    /**
     * @Assert\Type(type="string")
     */
    public $comment;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isMain;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}
