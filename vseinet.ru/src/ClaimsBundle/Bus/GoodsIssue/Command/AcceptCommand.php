<?php 

namespace ClaimsBundle\Bus\GoodsIssue\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class AcceptCommand extends Message
{
    /**
     * @VIA\Description("Goods issue id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;
}