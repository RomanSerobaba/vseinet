<?php 

namespace AppBundle\Bus\Cart\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class SetQuantityCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Выберите товар")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Укажите количество")
     * @Assert\Type(type="integer")
     */
    public $quantity;
}
