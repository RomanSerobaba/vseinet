<?php 

namespace ContentBundle\Bus\ParserProduct\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UnlinkCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Товар парсера")
     */
    public $id;
    
    /**
     * @Assert\NotBlank(message="Значение baseProductId не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Товар на сайте")
     */
    public $baseProductId;
}