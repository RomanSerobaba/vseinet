<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class ForgotCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Введете Ваш номер телефона или email")
     * @Assert\Type(type="string")
     */
    public $username;

    /**
     * @Assert\NotBlank(message="Отметьте флажок если вы человек")
     * @Assert\Type(type="boolean")
     */
    public $isHuman;
}
