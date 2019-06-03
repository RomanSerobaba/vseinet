<?php

namespace AppBundle\Bus\Security\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class ForgotCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Введете Ваш номер телефона или email")
     * @Assert\Type("string")
     */
    public $username;
}
