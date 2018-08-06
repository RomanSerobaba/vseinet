<?php 

namespace OrderBundle\Bus\Item\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetCommentsQuery extends Message
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     * @VIA\Description("Order item ID")
     */
    public $id;
}