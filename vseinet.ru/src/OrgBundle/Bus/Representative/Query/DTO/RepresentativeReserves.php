<?php 

namespace OrgBundle\Bus\Representative\Query\DTO;

use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class RepresentativeReserves
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Representative id")
     */
    public $representativeId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Reserve amount")
     */
    public $reserveAmount;
}