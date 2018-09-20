<?php 

namespace AdminBundle\Bus\Competitor\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetQuery extends Message
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;
}
