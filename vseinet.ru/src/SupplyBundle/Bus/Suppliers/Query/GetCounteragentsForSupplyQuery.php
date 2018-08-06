<?php 

namespace SupplyBundle\Bus\Suppliers\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetCounteragentsForSupplyQuery extends Message
{
    /**
     * @VIA\Description("Supply id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $supplyId;
}