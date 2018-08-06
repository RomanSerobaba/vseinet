<?php 

namespace OrgBundle\Bus\Representative\Query\DTO;

use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class PointsForShipping
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("ID")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("название")
     */
    public $name;
}