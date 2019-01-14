<?php

namespace AppBundle\Bus\Geo\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Address
{
    /**
     * @Assert\Type(type="integer")
     */
    public $geoStreetId;

    /**
     * @Assert\Type(type="string")
     */
    public $geoStreetName;

    /**
     * @Assert\Type(type="string")
     */
    public $house;

    /**
     * @Assert\Type(type="string")
     */
    public $building;

    /**
     * @Assert\Type(type="string")
     */
    public $apartment;

    /**
     * @Assert\Type(type="integer")
     */
    public $floor;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasLift = false;

    /**
     * @Assert\Type(type="integer")
     */
    public $office;

    /**
     * @Assert\Type(type="string")
     */
    public $postalCode;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoCityId;

    public function __construct($geoStreetId = NULL, $geoStreetName = NULL, $house = NULL, $building = NULL, $apartment = NULL, $floor = NULL, $hasLift = NULL, $office = NULL, $postalCode = NULL, $geoCityId = NULL)
    {
        $this->geoStreetId = (int) $geoStreetId;
        $this->geoStreetName = $geoStreetName;
        $this->house = $house;
        $this->building = $building;
        $this->apartment = $apartment;
        $this->floor = (int) $floor;
        $this->hasLift = (bool) $hasLift;
        $this->office = $office;
        $this->postalCode = $postalCode;
        $this->geoCityId = (int) $geoCityId;
    }
}
