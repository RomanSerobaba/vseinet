<?php 

namespace ContentBundle\Bus\Statistics\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class FulfillmentSummary
{    
    /**
     * @VIA\Description("Количество заполненных карточек")
     * @Assert\Type(type="integer")
     */
    public $count;


    public function __construct($count) 
    {
        $this->count = $count;
    }
}