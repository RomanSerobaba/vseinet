<?php 

namespace SuppliersBundle\Bus\Data\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class SetShippingInfoCommand extends Message
{
    /**
     * @VIA\Description("Supplier id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Threshold time")
     * @Assert\NotBlank
     * @Assert\Type(type="datetime")
     */
    public $orderThresholdTime;

    /**
     * @VIA\Description("Delivery time")
     * @Assert\NotBlank
     * @Assert\Type(type="datetime")
     */
    public $orderDeliveryTime;
}