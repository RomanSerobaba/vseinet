<?php 

namespace SupplyBundle\Bus\Invoices\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateArrivingTimeCommand extends Message
{
    /**
     * @VIA\Description("Supplier invoice id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Arriving time")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $time;
}