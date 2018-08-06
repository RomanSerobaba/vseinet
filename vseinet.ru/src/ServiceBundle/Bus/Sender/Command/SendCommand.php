<?php 

namespace ServiceBundle\Bus\Sender\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class SendCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение type не должно быть пустым")
     * @Assert\Type(type="string")
     */
    public $type;

    /**
     * @Assert\NotBlank(message="Value `data` should not be empty")
     * @Assert\Type(type="array")
     */
    public $data;
}