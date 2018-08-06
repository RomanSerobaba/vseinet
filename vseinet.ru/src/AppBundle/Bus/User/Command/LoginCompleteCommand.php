<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @internal
 */
class LoginCompleteCommand extends Message 
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;
}
