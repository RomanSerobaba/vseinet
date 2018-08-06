<?php 

namespace PricingBundle\Bus\Product\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetListQuery extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Код")
     * @Assert\NotBlank
     */
    public $baseProductId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Город")
     */
    public $cityId;
}