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
}
