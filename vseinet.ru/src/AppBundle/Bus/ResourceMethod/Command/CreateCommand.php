<?php 

namespace AppBundle\Bus\ResourceMethod\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение resourceId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $resourceId;

    /**
     * @Assert\Type(type="integer")
     */
    public $apiMethodId;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}