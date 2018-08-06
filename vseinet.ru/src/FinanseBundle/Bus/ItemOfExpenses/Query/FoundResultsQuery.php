<?php 

namespace FinanseBundle\Bus\ItemOfExpenses\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class FoundResultsQuery extends Message 
{
    
    /**
     * @VIA\Description("Стркоа поиска")
     * @Assert\Type(type="string")
     */
    public $q;
    
}