<?php

namespace AppBundle\Bus\Product\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FreeReserve
{
    /**
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @Assert\Type(type="integer")
     */
    public $destinationGeoPointId;

    /**
     * @Assert\Choice({"movement", "transit", "pallet", "other-free", "other-movement", "other-transit", "other-pallet"}, strict=true)
     */
    public $transitType;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @Assert\Date
     */
    public $arrivingDate;

    /**
     * @Assert\Type(type="integer")
     */
    public $arrivingGeoPointId;
}
