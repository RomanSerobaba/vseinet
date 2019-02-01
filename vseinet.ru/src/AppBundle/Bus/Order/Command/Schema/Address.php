<?php

namespace AppBundle\Bus\Order\Command\Schema;

use Symfony\Component\Validator\Constraints as Assert;

class Address
{
    /**
     * @Assert\Type(type="integer", message="Идентификатор улицы должен быть числом")
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
     * @Assert\Type(type="integer", message="Этаж должен быть числом")
     */
    public $floor;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasLift = false;

    /**
     * @Assert\Type(type="integer", message="Номер офиса должен быть числом")
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
        $this->setGeoStreetId($geoStreetId);
        $this->geoStreetName = $geoStreetName;
        $this->house = $house;
        $this->building = $building;
        $this->apartment = $apartment;
        $this->setFloor($floor);
        $this->setHasLift($hasLift);
        $this->office = $office;
        $this->postalCode = $postalCode;
        $this->setGeoCityId($geoCityId);
    }

    public function setGeoStreetId($geoStreetId)
    {
        $this->geoStreetId = (int) $geoStreetId;
    }

    public function setFloor($floor)
    {
        $this->floor = !empty($floor) ? (int) $floor : Null;
    }

    public function setHasLift($hasLift)
    {
        $this->hasLift = (bool) $hasLift;
    }

    public function setGeoCityId($geoCityId)
    {
        $this->geoCityId = (int) $geoCityId;
    }
}
