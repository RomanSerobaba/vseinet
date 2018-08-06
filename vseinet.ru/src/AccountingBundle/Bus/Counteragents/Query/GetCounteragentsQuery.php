<?php 

namespace AccountingBundle\Bus\Counteragents\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetCounteragentsQuery extends Message
{
    /**
     * @VIA\Description("Tin")
     * @Assert\Type(type="string")
     */
    public $tin;
}