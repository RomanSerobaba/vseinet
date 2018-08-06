<?php 

namespace ContentBundle\Bus\Category\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class MoveCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение pid не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $pid;
}