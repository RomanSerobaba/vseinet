<?php 

namespace SupplyBundle\Bus\Suppliers\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetShippingQuery extends Message
{
    /**
     * @VIA\Description("Для UNIT-тестирования")
     * @Assert\Type(type="boolean")
     * @VIA\DefaultValue(false)
     */
    public $isTest;
}