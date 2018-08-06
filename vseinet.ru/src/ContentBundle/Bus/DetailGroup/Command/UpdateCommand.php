<?php 

namespace ContentBundle\Bus\DetailGroup\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateCommand extends Message
{   
    /**
     * @Assert\NotBlank(message="Значение id не дожно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение name не дожно быть пустым")
     * @Assert\Type(type="string")
     */
    public $name;
}