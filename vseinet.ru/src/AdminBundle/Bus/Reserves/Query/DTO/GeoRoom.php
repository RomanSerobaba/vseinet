<?php

namespace AdminBundle\Bus\Reserves\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GeoRoom
{
    /**
     * @Assert\type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\type(type="integer")
     */
    public $geoPointId;

    /**
     * @Assert\All({
     *     @Assert\Type(type="AdminBundle\Bus\Reserves\Query\DTO\Supply")
     * })
     */
    public $supplies = [];

    /**
     * @Assert\Type(type="integer")
     */
    public $freeDelta = 0;

    /**
     * @Assert\Type(type="integer")
     */
    public $freeReservedDelta = 0;

    /**
     * @Assert\Type(type="integer")
     */
    public $freeTransitDelta = 0;

    /**
     * @Assert\Type(type="integer")
     */
    public $reservedDelta = 0;

    /**
     * @Assert\Type(type="integer")
     */
    public $reservedTransitDelta = 0;

    /**
     * @Assert\Type(type="integer")
     */
    public $issuedDelta = 0;

    /**
     * @Assert\Type(type="integer")
     */
    public $issuedTransitDelta = 0;


    public function __construct($id, $name, $geoPointId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->geoPointId = $geoPointId;
    }
}
