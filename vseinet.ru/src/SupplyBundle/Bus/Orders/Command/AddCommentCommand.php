<?php 

namespace SupplyBundle\Bus\Orders\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class AddCommentCommand extends Message
{
    /**
     * @VIA\Description("Order id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("User id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $userId;

    /**
     * @VIA\Description("Order item id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $orderItemId;

    /**
     * @VIA\Description("Comment")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $text;
}