<?php 

namespace ContentBundle\Bus\ColorPalette\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCommand extends Message
{    
    /**
     * @Assert\NotBlank(message="Value of 'name' should not be blank")
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}