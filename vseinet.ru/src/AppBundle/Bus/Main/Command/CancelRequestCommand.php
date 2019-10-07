<?php

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class CancelRequestCommand extends Message
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("integer", message="Ид заказа должен быть числом")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Укажите, пожалуйста, причину отказа")
     * @Assert\Type("string")
     */
    public $comment;

    public function setId($id)
    {
        $this->id = null !== $id ? (int) $id : $id;
    }
}
