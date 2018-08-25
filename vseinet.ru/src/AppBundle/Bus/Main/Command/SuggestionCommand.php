<?php 

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class SuggestionCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Оставте сообщение")
     * @Assert\Type(type="string")
     */
    public $text;

    /**
     * @Assert\Type(type="AppBundle\Bus\User\Query\DTO\UserData")
     * @Assert\Valid
     */
    public $userData;
}
