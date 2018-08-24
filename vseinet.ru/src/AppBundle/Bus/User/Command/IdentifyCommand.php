<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class IdentifyCommand extends Message
{
    /**
     * @Assert\Type(type="AppBundle\Bus\User\Query\DTO\UserData")
     */
    public $userData;
}
