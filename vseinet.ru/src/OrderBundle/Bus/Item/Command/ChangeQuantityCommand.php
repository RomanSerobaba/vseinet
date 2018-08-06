<?php 

namespace OrderBundle\Bus\Item\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ChangeQuantityCommand extends Message
{
    /**
     * @VIA\Description("Order item id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Quantity")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @VIA\Description("Тип (не указывать)")
     * @Assert\NotBlank
     * @Assert\Choice({"order", "request"}, strict=true)
     */
    public $type;
}