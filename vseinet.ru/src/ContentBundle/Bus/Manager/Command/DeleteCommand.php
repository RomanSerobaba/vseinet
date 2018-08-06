<?php 

namespace ContentBundle\Bus\Manager\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение userId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $userId;
}