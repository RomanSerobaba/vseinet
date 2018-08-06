<?php 

namespace ContentBundle\Bus\CategorySection\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Не указан код раздела категории")
     * @Assert\Type(type="integer")
     */
    public $id;
}