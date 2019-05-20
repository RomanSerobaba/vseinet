<?php

namespace AppBundle\Bus\Product\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DeliveryDate
{
    /**
     * Assert\Date.
     *
     * @VIA\Description("Дата доставки")
     */
    public $date;

    public function __construct($date = null)
    {
        $this->date = $date;
    }

    public function setDate($date)
    {
        if (null === $this->date || $this->date > $date) {
            $this->date = $date;
        }
    }
}
