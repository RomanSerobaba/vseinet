<?php

namespace AdminBundle\Bus\Reserves\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GeoPoint
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\type(type="integer")
     */
    public $geoCityId;

    /**
     * @Assert\All({
     *     @Assert\Type(type="integer")
     * })
     */
    public $geoRoomIds = [];

    /**
     * @Assert\Type(type="integer")
     */
    public $freeDelta = 0;

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

    /**
     * @Assert\Type(type="integer")
     */
    public $pricetag;

    /**
     * @Assert\Type(type="boolean")
     */
    public $pricetagIsHandmade;


    public function __construct($id, $name, $geoCityId, $pricetag, $pricetagIsHandmade)
    {
        $this->id = $id;
        $this->name = $name;
        $this->geoCityId = $geoCityId;
        $this->pricetag = $pricetag;
        $this->pricetagIsHandmade = $pricetagIsHandmade;
    }
}
