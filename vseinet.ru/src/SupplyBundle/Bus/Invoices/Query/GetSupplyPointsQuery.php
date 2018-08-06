<?php 

namespace SupplyBundle\Bus\Invoices\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetSupplyPointsQuery extends Message
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     * @VIA\Description("Supply id")
     */
    public $id;
}