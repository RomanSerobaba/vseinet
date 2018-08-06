<?php 

namespace SupplyBundle\Bus\Data\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetSupplyItemsForShippingQuery extends Message
{
    /**
     * @VIA\Description("Supply id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;
}