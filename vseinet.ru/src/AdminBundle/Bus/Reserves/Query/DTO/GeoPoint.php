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
    public $pricetag;

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
    public $pricetagDate;

    /**
     * @Assert\Type(type="string")
     */
    public $pricetagCreator;

    /**
     * @Assert\Type(type="datetime")
     */
    public $handmadePricetagDate;

    /**
     * @Assert\Type(type="string")
     */
    public $handmadePricetagCreator;


    public function __construct($id, $name, $geoCityId, $pricetag, $pricetagIsHandmade, $handmadePricetag, $pricetagDate, $pricetagCreator, $handmadePricetagDate, $handmadePricetagCreator)
    {
        $this->id = $id;
        $this->name = $name;
        $this->geoCityId = $geoCityId;
        $this->pricetag = $pricetag;
        $this->pricetagIsHandmade = $pricetagIsHandmade;
        $this->handmadePricetag = $handmadePricetag;
        $this->pricetagDate = $pricetagDate;
        $this->pricetagCreator = $pricetagCreator;
        $this->handmadePricetagDate = $handmadePricetagDate;
        $this->handmadePricetagCreator = $handmadePricetagCreator;
    }
}
