<?php 

namespace ReservesBundle\Bus\GoodsAcceptance\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class StatusesQuery extends Message 
{
    /**
     * @VIA\Description("Показывать только используемые на текущий момент статусы")
     * @Assert\Type(type="boolean")
     * @VIA\DefaultValue(true)
     */
    public $onlyActive;
    
}