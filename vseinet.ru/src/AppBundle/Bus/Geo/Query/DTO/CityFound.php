<?php

namespace AppBundle\Bus\Geo\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CityFound
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     */
    public $unit;

    /**
     * @Assert\Type(type="string")
     */
    public $regionName;


    public function __construct($id, $name, $unit, $geoRegionName, $geoAreaName)
    {
        $this->id = $id;
        $this->name = $name;
        $this->unit = $unit;
        $this->regionName = $geoRegionName;
        if ($geoAreaName) {
            $this->regionName .= ' / '.$geoAreaName;
        }
    }
}
