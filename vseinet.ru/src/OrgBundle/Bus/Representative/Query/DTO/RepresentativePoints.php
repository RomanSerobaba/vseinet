<?php 

namespace OrgBundle\Bus\Representative\Query\DTO;

use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class RepresentativePoints
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("ID")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("код")
     */
    public $code;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("название")
     */
    public $name;
}