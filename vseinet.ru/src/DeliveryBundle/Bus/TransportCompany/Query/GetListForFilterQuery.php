<?php 

namespace DeliveryBundle\Bus\TransportCompany\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;
use AppBundle\Validator\Constraints as VIC;

class GetListForFilterQuery extends Message
{
    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("Только активные")
     */
    public $isActive;    
}