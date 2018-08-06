<?php 

namespace SupplyBundle\Bus\Sms\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class SendCommand extends Message
{
    /**
     * @VIA\Description("Order id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Comment")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $text;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}