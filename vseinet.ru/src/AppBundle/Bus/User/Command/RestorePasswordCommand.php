<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class RestorePasswordCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Придумайте пароль")
     * @Assert\Type(type="string")
     */
    public $password;

    /**
     * @Assert\NotBlank(message="Повторите пароль")
     * @Assert\Type(type="string")
     */
    public $passwordConfirm;
}
