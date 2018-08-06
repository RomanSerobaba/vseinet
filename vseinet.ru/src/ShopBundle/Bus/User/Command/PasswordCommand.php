<?php 

namespace ShopBundle\Bus\User\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordCommand extends Message
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $passwordCurrent;

    /**
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $password;

    /**
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $passwordConfirm;
}