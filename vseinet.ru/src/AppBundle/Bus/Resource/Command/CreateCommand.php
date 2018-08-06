<?php 

namespace AppBundle\Bus\Resource\Command;

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
     * @Assert\NotBlank(message="Значение groupId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $groupId;

    /**
     * @Assert\Type(type="string")
     */
    public $path;

    /**
     * @Assert\Type(type="string")
     */
    public $description;  
    
    /**
     * @Assert\Type(type="boolean")
     */
    public $isMenu;  

    /**
     * @Assert\Uuid
     */
    public $uuid;
}