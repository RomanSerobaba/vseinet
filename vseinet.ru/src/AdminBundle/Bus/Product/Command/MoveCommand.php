<?php 

namespace AdminBundle\Bus\Product\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class MoveCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Выберите товар")
     * @Assert\Type(type="numeric")
     */
    public $id;

    /**
     * @Assert\Type(type="AdminBundle\Bus\Category\Command\ChoiceCommand")
     */
    public $category;
}
