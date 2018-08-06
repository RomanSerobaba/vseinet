<?php 

namespace ContentBundle\Bus\Naming\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение detailId не может быть пустым")
     * @Assert\Type(type="integer")
     */
    public $detailId;
}