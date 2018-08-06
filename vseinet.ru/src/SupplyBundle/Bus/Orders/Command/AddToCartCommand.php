<?php 

namespace SupplyBundle\Bus\Orders\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class AddToCartCommand extends Message
{
    /**
     * @VIA\Description("Base product id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @VIA\Description("Price")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $price;
}