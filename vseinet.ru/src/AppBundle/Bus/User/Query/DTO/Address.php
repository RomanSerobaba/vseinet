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
    public $regionName;

    /**
     * @Assert\Type(type="string")
     */
    public $regionUnit;

    /**
     * @Assert\Type(type="string") 
     */
    public $areaName;

    /**
     * @Assert\Type(type="string")
     */
    public $areaUnit;

    /**
     * @Assert\type(type="string")
     */
    public $cityName;

    /**
     * @Assert\Type(type="string")
     */
    public $cityUnit;

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
     * @Assert\Type(type="AppBundle\Doctrine\DBAL\ValueObject\Point")
     */
    public $coordinates;

    /**
     * @Assert\Type(type="string")
     */
    public $comment;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isMain;

    /**
     * @Assert\Type(type="string")
     */
    public $address;


    public function __construct(
        $id,
        $postalCode,
        $regionName,
        $regionUnit,
        $areaName,
        $areaUnit,
        $cityName,
        $cityUnit,
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
        $isMain
    ) {
        $this->id = $id;
        $this->postalCode = $postalCode;
        $this->regionName = $regionName;
        $this->regionUnit = $regionUnit;
        $this->areaName = $areaName;
        $this->areaUnit = $areaUnit;
        $this->cityName = $cityName;
        $this->cityUnit = $cityUnit;
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
        $this->isMain = $isMain; 

        $components = [];
        if ($postalCode) {
            $components[] = $postalCode;
        }
        if ($regionName) {
            $components[] = $regionName.' '.$regionUnit;
        }
        if ($cityName) {
            $components[] = $cityName.' '.$cityUnit;
        }
        if ($streetName) {
            $components[] = $streetName.' '.$streetUnit;
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

        $this->address = implode(', ', $components);
    }
}
