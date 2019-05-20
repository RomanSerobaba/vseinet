<?php

namespace AppBundle\Bus\Order\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class ProductAvailability
{
    /**
     * @VIA\Enum("AppBundle\Enum\ProductAvailabilityCode")
     */
    public $availability;

    /**
     * @Assert\Date
     */
    public $deliveryDate;

    public function __construct($availability)
    {
        $this->availability = $availability;
    }
}
