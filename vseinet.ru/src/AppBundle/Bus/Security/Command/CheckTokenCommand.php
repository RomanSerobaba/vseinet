<?php

namespace AppBundle\Bus\Security\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class CheckTokenCommand extends Message
{
    /**
     * @Assert\Type("string")
     */
    public $code;

    /**
     * @Assert\Type("string")
     */
    public $hash;
}
