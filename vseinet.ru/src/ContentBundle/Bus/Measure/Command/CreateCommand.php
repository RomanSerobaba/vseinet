<?php 

namespace ContentBundle\Bus\Measure\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение name не должно быть пустым")
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}