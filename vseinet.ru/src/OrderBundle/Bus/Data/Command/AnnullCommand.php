<?php 

namespace OrderBundle\Bus\Data\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class AnnullCommand extends Message
{
    /**
     * @VIA\Description("Order item ids")
     * @Assert\NotBlank
     * @Assert\Type(type="array")
     */
    public $ids;

    /**
     * @VIA\Description("Cause code")
     * @Assert\Type(type="string")
     */
    public $causeCode;

    /**
     * @VIA\Description("Comment")
     * @Assert\Type(type="string")
     */
    public $comment;

    /**
     * @VIA\Description("Client fault")
     * @Assert\Type(type="boolean")
     * @VIA\DefaultValue(false)
     */
    public $isClientFault;
}