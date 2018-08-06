<?php

namespace ContentBundle\Bus\Measure\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class All
{
    /**
     * @Assert\Type(type="array<ContentBundle\Bus\Measure\Query\DTO\Measure>")
     */
    public $measures;

    /**
     * @Assert\Type(type="array<ContentBundle\Bus\Measure\Query\DTO\MeasureUnit>")
     */
    public $measureUnits;
    

    public function __construct($measures, $measureUnits)
    {
        $this->measures = array_values($measures);
        $this->measureUnits = array_values($measureUnits);
    }
}

