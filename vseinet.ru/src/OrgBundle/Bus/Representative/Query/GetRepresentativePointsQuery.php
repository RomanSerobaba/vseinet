<?php 

namespace OrgBundle\Bus\Representative\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetRepresentativePointsQuery extends Message
{
    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("Is retail only")
     * @VIA\DefaultValue("true")
     */
    public $isRetailOnly;
}