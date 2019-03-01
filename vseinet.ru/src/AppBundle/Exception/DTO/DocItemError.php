<?php

namespace AppBundle\Exception\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DocItemError
{
    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Идентификатор строки табличной части")
     */
    public $key;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Сообщение ошибки")
     */
    public $message;

    /**
     * @param mixin  $key
     * @param string $message
     */
    public function __construct($key, string $message)
    {
        $this->key = $key;
        $this->message = $message;
    }
}
