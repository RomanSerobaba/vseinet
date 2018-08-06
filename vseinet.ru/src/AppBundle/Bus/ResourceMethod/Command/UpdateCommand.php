<?php 

namespace AppBundle\Bus\ResourceMethod\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="sinteger")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение resourceId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $resourceId;

    /**
     * @Assert\Type(type="integer")
     */
    public $apiMethodId;  
}