<?php 

namespace AppBundle\Bus\Resource\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class DenyUserCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение userId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $userId;

    /**
     * @Assert\NotBlank(message="Значение resourceId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $resourceId;
}