<?php

namespace AdminBundle\Bus\Reserves\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GeoObject
{
    /**
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;

    /**
     * @Assert\Type(type="string")
     */
    public $geoRoomName;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @Assert\Type(type="string")
     */
    public $geoPointName;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoCityId;

    /**
     * @Assert\Type(type="string")
     */
    public $geoCityName;

    /**
     * @Assert\Type(type="integer")
     */
    public $pricetag;

    /**
     * @Assert\Type(type="datetime")
     */
    public $pricetagDate;

    /**
     * @Assert\Type(type="string")
     */
    public $pricetagCreator;

    /**
     * @Assert\Type(type="boolean")
     */
    public $pricetagIsHandmade;

    /**
     * @Assert\Type(type="integer")
     */
    public $handmadePricetag;

    /**
     * @Assert\Type(type="datetime")
     */
    public $handmadePricetagDate;

    /**
     * @Assert\Type(type="string")
     */
    public $handmadePricetagCreator;
}
