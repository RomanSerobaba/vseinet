<?php

namespace OrgBundle\Bus\Representative\Query;

use AppBundle\Annotation as VIA;
use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class FoundResultsQuery extends Message
{
    /**
     * @Assert\Type(type="string")
     */
    public $q;

    /**
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(50)
     */
    public $limit;

    /**
     * @Assert\Type(type="boolean")
     */
    public $withInactives;
}