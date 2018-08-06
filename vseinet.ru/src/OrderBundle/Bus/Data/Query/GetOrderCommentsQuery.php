<?php 

namespace OrderBundle\Bus\Data\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetOrderCommentsQuery extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Заказ")
     */
    public $id;
}