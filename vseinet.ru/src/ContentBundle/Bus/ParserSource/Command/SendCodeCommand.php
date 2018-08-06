<?php 

namespace ContentBundle\Bus\ParserSource\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

/**
 * @deprecated
 */
class SendCodeCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="id")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение type не должно быть пустым")
     * @Assert\Choice({"prices", "products", "images", "supplier_products", "tradings"}, strict=true)
     */
    public $type;
}