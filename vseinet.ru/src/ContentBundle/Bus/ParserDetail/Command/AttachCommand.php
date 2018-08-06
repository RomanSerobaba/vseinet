<?php 

namespace ContentBundle\Bus\ParserDetail\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class AttachCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Характеристика парсера")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение detailId не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Характеристика товара")
     */
    public $detailId;
}