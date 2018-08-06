<?php

namespace OrgBundle\Bus\Work\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetPlanResultsQuery extends Message
{
    /**
     * @Assert\Type(type="string")
     * @Assert\Date()
     */
    public $since;

    /**
     * @Assert\Type(type="string")
     * @Assert\Date()
     */
    public $till;

    /**
     * @Assert\Choice({"reclamation", "overstocked", "orders"}, strict=true, multiple=true)
     */
    public $types;
}