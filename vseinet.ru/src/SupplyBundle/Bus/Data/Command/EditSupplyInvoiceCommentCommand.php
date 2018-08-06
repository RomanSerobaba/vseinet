<?php 

namespace SupplyBundle\Bus\Data\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class EditSupplyInvoiceCommentCommand extends Message
{
    /**
     * @VIA\Description("Supply invoice id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Comment")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $comment;
}