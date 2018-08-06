<?php 

namespace PricingBundle\Bus\Competitors\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GetList
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
     * @Assert\Type(type="string")
     */
    public $link;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isActive;

    /**
     * @Assert\Type(type="integer")
     */
    public $supplierId;

    /**
     * @Assert\Type(type="string")
     */
    public $supplier;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoCityId;

    /**
     * @Assert\Type(type="string")
     */
    public $city;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoStreetId;

    /**
     * @Assert\Type(type="string")
     */
    public $address;

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
    public $floor;

    /**
     * @Assert\Type(type="integer")
     */
    public $checkingCount;

    /**
     * @Assert\Type(type="integer")
     */
    public $failedCount;

    /**
     * @Assert\Type(type="integer")
     */
    public $successfulCount;

    /**
     * @Assert\Type(type="integer")
     */
    public $competitiveCount;

    /**
     * @Assert\Type(type="integer")
     */
    public $loosingCount;

    /**
     * @Assert\Type(type="string")
     */
    public $typeCode;

    /**
     * GetList constructor.
     * @param $id
     * @param $name
     * @param $link
     * @param $isActive
     * @param $supplierId
     * @param $supplier
     * @param $geoCityId
     * @param $city
     * @param $geoStreetId
     * @param $address
     * @param $house
     * @param $building
     * @param $floor
     * @param $checkingCount
     * @param $failedCount
     * @param $successfulCount
     * @param $competitiveCount
     * @param $loosingCount
     * @param $typeCode
     */
    public function __construct($id, $name, $link, $isActive, $supplierId, $supplier, $geoCityId, $city, $geoStreetId, $address, $house, $building, $floor, $checkingCount, $failedCount, $successfulCount, $competitiveCount, $loosingCount, $typeCode)
    {
        $this->id = $id;
        $this->name = $name;
        $this->link = $link;
        $this->isActive = $isActive;
        $this->supplierId = $supplierId;
        $this->supplier = $supplier;
        $this->geoCityId = $geoCityId;
        $this->city = $city;
        $this->geoStreetId = $geoStreetId;
        $this->address = $address;
        $this->house = $house;
        $this->building = $building;
        $this->floor = $floor;
        $this->checkingCount = $checkingCount;
        $this->failedCount = $failedCount;
        $this->successfulCount = $successfulCount;
        $this->competitiveCount = $competitiveCount;
        $this->loosingCount = $loosingCount;
        $this->typeCode = $typeCode;
    }
}