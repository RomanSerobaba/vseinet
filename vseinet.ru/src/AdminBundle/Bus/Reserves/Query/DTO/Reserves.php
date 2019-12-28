<?php

namespace AdminBundle\Bus\Reserves\Query\DTO;

use AppBundle\Enum\ProductAvailabilityCode;
use Symfony\Component\Validator\Constraints as Assert;

class Reserves
{
    /**
     * @Assert\All({
     *     @Assert\Type(type="AdminBundle\Bus\Reserves\Query\DTO\GeoCity")
     * })
     */
    public $geoCities;

    /*
     * @Assert\All({
     *     @Assert\Type(type="AdminBundle\Bus\Reserves\Query\DTO\GeoPoint")
     * })
     */
    public $geoPoints;

    /*
     * @Assert\All({
     *     @Assert\Type(type="AdminBundle\Bus\Reserves\Query\DTO\GeoRoom")
     * })
     */
    public $geoRooms;

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
    public $freeAssembledDelta = 0;

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
    public $reservedAssembledDelta = 0;

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

    /**
     * @Assert\Type(type="integer")
     */
    public $remainsPurchasePrice = 0;

    /**
     * @Assert\Type(type="integer")
     */
    public $supplierPurchasePrice = 0;

    /**
     * @Enum("AppBundle\Enum\ProductAvailabilityCode")
     */
    public $supplierProductAvailabilityCode;


    public function __construct(array $geoCities = [], array $geoPoints = [], array $geoRooms = [])
    {
        $this->geoCities = $geoCities;
        $this->geoPoints = $geoPoints;
        $this->geoRooms = $geoRooms;
    }
}
