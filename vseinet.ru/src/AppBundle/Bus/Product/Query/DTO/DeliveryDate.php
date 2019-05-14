<?php

namespace AppBundle\Bus\Product\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DeliveryDate
{
    /**
     * @VIA\Enum("AppBundle\Enum\ProductAvailabilityCode")
     */
    public $availability;

    /**
     * @Assert\Type("AppBundle\Bus\Product\Query\DTO\GeoPoint")
     * @VIA\Description("Адреса магазинов с указания количества товара в наличии")
     */
    public $geoPoints = [];

    /**
     * Assert\Date.
     *
     * @VIA\Description("Дата доставки")
     */
    public $date;

    public function __construct($availability)
    {
        $this->availability = $availability;
    }

    public function setDate($date)
    {
        if (null === $this->date || $this->date > $date) {
            $this->date = $date;
        }
    }
}
