<?php 

namespace SupplyBundle\Bus\Orders\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class UpdatePaymentTypeCommand extends Message
{
    /**
     * @VIA\Description("Order id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Payment type")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $type;
}