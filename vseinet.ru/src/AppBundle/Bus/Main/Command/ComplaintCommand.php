<?php

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;

class ComplaintCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Выберите причину")
     * @Enum("AppBundle\Enum\ComplaintType")
     */
    public $type;

    /**
     * @Assert\Type("string")
     */
    public $managerName;

    /**
     * @Assert\Type("string")
     */
    public $managerPhone;

    /**
     * @Assert\NotBlank(message="Оставьте сообщение")
     * @Assert\Type("string")
     */
    public $text;

    /**
     * @Assert\Type("AppBundle\Bus\User\Query\DTO\UserData")
     * @Assert\Valid
     */
    public $userData;
}
