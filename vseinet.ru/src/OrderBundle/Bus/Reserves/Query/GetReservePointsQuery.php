<?php 

namespace OrderBundle\Bus\Reserves\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetReservePointsQuery extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @VIA\Description("Order item id")
     */
    public $id;
}