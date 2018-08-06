<?php

namespace OrgBundle\Bus\Counteragents\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetFinancialResourcesQuery extends Message
{
    /**
     * @VIA\Description("Type of financial resources")
     * @Assert\Type(type="string")
     */
    public $type;
}