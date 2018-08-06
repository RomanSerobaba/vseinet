<?php 

namespace ClaimsBundle\Bus\GoodsIssue\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class SetProductDecisionCommand extends Message
{
    /**
     * @VIA\Description("Goods issue id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Decision")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     * @Assert\Choice({"returned_on_balance", "removed_from_balance", "returned_to_client"}, strict=true)
     */
    public $decision;

    /**
     * @VIA\Description("Room id")
     * @Assert\Type(type="integer")
     */
    public $returned_on_balance_room_id;

    /**
     * @VIA\Description("Comment")
     * @Assert\Type(type="string")
     */
    public $comment;
}