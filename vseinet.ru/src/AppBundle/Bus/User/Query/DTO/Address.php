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
     * @Assert\Type(type="integer")
     */
    public $regionId;

    /**
     * @Assert\Type(type="string")
     */
    public $regionName;

    /**
     * @Assert\Type(type="string")
     */
    public $regionUnit;

    /**
     * @Assert\Type(type="integer")
     */
    public $cityId;

    /**
     * @Assert\type(type="string")
     */
    public $cityName;

    /**
     * @Assert\Type(type="string")
     */
    public $cityUnit;

    /**
     * @Assert\Type(type="integer")
     */
    public $streetId;

    /**
     * @Assert\Type(type="string")
     */
    public $streetName;

    /**
     * @Assert\Type(type="string")
     */
    public $streetUnit;

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
     * @Assert\Type(type="string")
     */
    public $coordinates;

    /**
     * @Assert\Type(type="string")
     */
    public $comment;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isDefault;

    /**
     * @Assert\Type(type="string")
     */
    public $address;


    public function __construct(
        $id,
        $regionId,
        $regionName,
        $regionUnit,
        $cityId,
        $cityName,
        $cityUnit,
        $streetId,
        $streetName,
        $streetUnit,
        $house,
        $building,
        $apartment,
        $office,
        $floor,
        $hasLift,
        $coordinates,
        $comment,
        $isDefault
    ) {
        $this->id = $id;
        $this->regionId = $regionId;
        $this->regionName = $regionName;
        $this->regionUnit = $regionUnit;
        $this->cityId = $cityId;
        $this->cityName = $cityName;
        $this->cityUnit = $cityUnit;
        $this->streetId = $streetId;
        $this->streetName = $streetName;
        $this->streetUnit = $streetUnit;
        $this->house = $house;
        $this->building = $building;
        $this->apartment = $apartment;
        $this->office = $office;
        $this->floor = $floor;
        $this->hasLift = $hasLift;
        $this->coordinates = $coordinates;
        $this->comment = $comment;
        $this->isDefault = $isDefault; 

        $fragments = [];
        if ($regionName) {
            $fragments[] = $regionName.' '.$regionUnit;
        }
        if ($cityName) {
            $fragments[] = $cityName.' '.$cityUnit;
        }
        if ($streetName) {
            $fragments[] = $streetName.' '.$streetUnit;
        }
        if ($house) {
            $fragments[] = 'д '.$house;
        }
        if ($building) {
            $fragments[] = 'стр-е '.$building;
        }
        if ($apartment) {
            $fragments[] = 'кв '.$apartment;
        }
        if ($office) {
            $fragments[] = 'оф '.$office;
        }

        $this->address = implode(', ', $fragments);
    }
}
