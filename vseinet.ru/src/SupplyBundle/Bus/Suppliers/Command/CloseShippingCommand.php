<?php 

namespace SupplyBundle\Bus\Suppliers\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class CloseShippingCommand extends Message
{
    /**
     * @VIA\Description("Supplier id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;
}