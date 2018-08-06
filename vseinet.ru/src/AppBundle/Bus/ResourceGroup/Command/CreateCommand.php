<?php 

namespace AppBundle\Bus\ResourceGroup\Command;

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
     * @Assert\NotBlank(message="Значение clientId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $clientId;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}