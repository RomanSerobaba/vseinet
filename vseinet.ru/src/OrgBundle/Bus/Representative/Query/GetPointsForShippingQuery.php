<?php

namespace OrgBundle\Bus\Representative\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetPointsForShippingQuery extends Message
{
    /**
     * @VIA\Description("Supplier id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $supplierId;
}