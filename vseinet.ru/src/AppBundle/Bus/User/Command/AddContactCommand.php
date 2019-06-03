<?php

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;

class AddContactCommand extends Message
{
    /**
     * @Assert\Type("integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Выберите тип контакта")
     * @Enum("AppBundle\Enum\ContactTypeCode")
     */
    public $typeCode;

    /**
     * @Assert\Type("string")
     */
    public $typeCodeName;

    /**
     * @Assert\NotBlank(message="Введите контакт")
     * @Assert\Type("string")
     */
    public $value;

    /**
     * @Assert\Type("string")
     */
    public $comment;

    /**
     * @Assert\Type("boolean")
     */
    public $isMain;
}
