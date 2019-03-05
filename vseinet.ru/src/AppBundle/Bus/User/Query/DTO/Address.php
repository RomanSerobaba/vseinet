<?php

namespace AppBundle\Bus\User\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Address
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $postalCode;

    /**
     * @Assert\Type(type="string")
     */
    public $geoRegionName;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoRegionId;

    /**
     * @Assert\Type(type="string")
     */
    public $geoRegionUnit;

    /**
     * @Assert\Type(type="string")
     */
    public $geoAreaName;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoAreaId;

    /**
     * @Assert\Type(type="string")
     */
    public $geoAreaUnit;

    /**
     * @Assert\type(type="string")
     */
    public $geoCityName;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoCityId;

    /**
     * @Assert\Type(type="string")
     */
    public $geoCityUnit;

    /**
     * @Assert\Type(type="string")
     */
    public $geoStreetName;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoStreetId;

    /**
     * @Assert\Type(type="string")
     */
    public $geoStreetUnit;

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
     * @Assert\Type(type="string")
     */
    public $office;

    /**
     * @Assert\Type(type="integer")
     */
    public $floor;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasLift;

    /**
     * @Assert\Type(type="AppBundle\Doctrine\DBAL\ValueObject\Point")
     */
    public $coordinates;

    /**
     * @Assert\Type(type="string")
     */
    public $address;

    /**
     * @Assert\Type(type="string")
     */
    public $comment;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isMain;

    public function __construct(
        $id,
        $postalCode,
        $geoRegionName,
        $geoRegionId,
        $geoRegionUnit,
        $geoAreaName,
        $geoAreaId,
        $geoAreaUnit,
        $geoCityName,
        $geoCityId,
        $geoCityUnit,
        $geoStreetName,
        $geoStreetId,
        $geoStreetUnit,
        $house,
        $building,
        $apartment,
        $office,
        $floor,
        $hasLift,
        $coordinates,
        $address,
        $comment,
        $isMain
    ) {
        $this->id = $id;
        $this->postalCode = $postalCode;
        $this->geoRegionName = $geoRegionName;
        $this->geoRegionId = $geoRegionId;
        $this->geoRegionUnit = $geoRegionUnit;
        $this->geoAreaName = $geoAreaName;
        $this->geoAreaId = $geoAreaId;
        $this->geoAreaUnit = $geoAreaUnit;
        $this->geoCityName = $geoCityName;
        $this->geoCityId = $geoCityId;
        $this->geoCityUnit = $geoCityUnit;
        $this->geoStreetName = $geoStreetName;
        $this->geoStreetId = $geoStreetId;
        $this->geoStreetUnit = $geoStreetUnit;
        $this->house = $house;
        $this->building = $building;
        $this->apartment = $apartment;
        $this->office = $office;
        $this->floor = $floor;
        $this->hasLift = $hasLift;
        $this->coordinates = $coordinates;
        $this->address = $address;
        $this->comment = $comment;
        $this->isMain = $isMain;

        $components = [];
        if ($postalCode) {
            $components[] = $postalCode;
        }
        if ($geoRegionName) {
            $components[] = $geoRegionName.' '.$geoRegionUnit;
        }
        if ($geoCityName) {
            $components[] = $geoCityName.' '.$geoCityUnit;
        }
        if ($geoAreaName) {
            $components[] = $geoAreaName.' '.$geoAreaUnit;
        }
        if ($geoStreetName) {
            $components[] = $geoStreetName.' '.$geoStreetUnit;
        }
        if ($house) {
            $components[] = 'д '.$house;
        }
        if ($building) {
            $components[] = 'стр-е '.$building;
        }
        if ($apartment) {
            $components[] = 'кв '.$apartment;
        }
        if ($office) {
            $components[] = 'оф '.$office;
        }
        if (!empty($components)) {
            $this->address = implode(', ', array_map(function ($component) { return trim($component); }, $components));
        }
    }
}
