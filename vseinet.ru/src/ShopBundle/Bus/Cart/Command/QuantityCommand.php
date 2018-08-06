<?php 

namespace ShopBundle\Bus\Cart\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class QuantityCommand extends Message
{
    /**
     * @VIA\Description("Product id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Количество")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $quantity;
}