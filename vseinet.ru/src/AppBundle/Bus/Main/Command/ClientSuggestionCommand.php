<?php

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class ClientSuggestionCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Оставьте сообщение")
     * @Assert\Type("string")
     */
    public $text;

    /**
     * @Assert\Type("AppBundle\Bus\User\Query\DTO\UserData")
     * @Assert\Valid
     */
    public $userData;
}
