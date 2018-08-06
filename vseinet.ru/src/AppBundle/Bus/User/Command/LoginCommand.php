<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class LoginCommand extends Message 
{
    /**
     * @VIA\Description("Мобильный телефон или email")
     * @Assert\NotBlank(message="Введите мобильный телефон или email")
     * @Assert\Type(type="string")
     */
    public $username;

    /**
     * @Assert\NotBlank(message="Введите пароль")
     * @Assert\Type(type="string")
     */
    public $password;

    /**
     * @Assert\Type(type="boolean")
     */
    public $remember;
}