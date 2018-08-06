<?php 

namespace ClaimsBundle\Bus\GoodsIssue\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class SetClientDecisionCommand extends Message
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
     * @Assert\Choice({"returned_money", "returned_goods"}, strict=true)
     */
    public $decision;

    /**
     * @VIA\Description("За вычетом неустойки")
     * @Assert\Type(type="integer")
     */
    public $forfeit;

    /**
     * @VIA\Description("Comment")
     * @Assert\Type(type="string")
     */
    public $comment;
}