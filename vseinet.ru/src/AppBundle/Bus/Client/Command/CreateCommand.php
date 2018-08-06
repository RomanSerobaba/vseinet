<?php 

namespace AppBundle\Bus\Client\Command;

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
     * @Assert\NotBlank(message="Значение redirectUri не должно быть пустым")
     * @Assert\Type(type="string")
     */
    public $redirectUri;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}