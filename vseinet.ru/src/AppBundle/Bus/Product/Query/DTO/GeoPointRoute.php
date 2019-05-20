<?php

namespace AppBundle\Bus\Product\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GeoPointRoute
{
    /**
     * @Assert\Type("integer")
     */
    public $startingPointId;

    /**
     * @Assert\Type("integer")
     */
    public $arrivalPointId;

    /**
     * @Assert\Type("string")
     */
    public $schedule;
}
