<?php 

namespace ShopBundle\Bus\Cart\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class DiscountCommand extends Message
{
    /**
     * @VIA\Description("Код")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $code;
}