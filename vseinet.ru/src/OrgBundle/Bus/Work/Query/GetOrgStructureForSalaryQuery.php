<?php 

namespace OrgBundle\Bus\Work\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetOrgStructureForSalaryQuery extends Message
{
    /**
     * @Assert\Type(type="string")
     * @Assert\Date()
     */
    public $date;
}
