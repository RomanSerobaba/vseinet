<?php 

namespace SupplyBundle\Bus\Orders\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class CancelPositionCommand extends Message
{
    /**
     * @VIA\Description("Order item ID")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $orderItemId;

    /**
     * @VIA\Description("Base product id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $baseProductId;
}