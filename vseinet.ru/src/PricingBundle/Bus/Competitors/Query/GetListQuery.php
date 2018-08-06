<?php 

namespace PricingBundle\Bus\Competitors\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetListQuery extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Город")
     * @VIA\DefaultValue(null)
     */
    public $cityId;
}