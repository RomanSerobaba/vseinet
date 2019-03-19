<?php

namespace AppBundle\Exception\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class ParameterError
{
    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наименование входящего параметра")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Сообщение ошибки")
     */
    public $message;

    /**
     * @param string $name
     * @param string $message
     */
    public function __construct(string $name, string $message)
    {
        $this->name = $name;
        $this->message = $message;
    }
}
