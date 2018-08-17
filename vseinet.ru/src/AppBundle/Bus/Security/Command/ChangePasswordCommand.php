<?php 

namespace AppBundle\Bus\Security\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Введите текущий пароль")
     * @Assert\Type(type="string")
     */
    public $password;

    /**
     * @Assert\NotBlank(message="Придумайте новый пароль")
     * @Assert\Type(type="string")
     */
    public $newPassword;

    /**
     * @Assert\NotBlank(message="Повторите новый пароль")
     * @Assert\Type(type="string")
     */
    public $newPasswordConfirm;
}
