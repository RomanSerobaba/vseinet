<?php

namespace ThirdPartyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoAddress
 *
 * @ORM\Table(name="geo_address")
 * @ORM\Entity(repositoryClass="ThirdPartyBundle\Repository\GeoAddressRepository")
 */
class GeoAddress
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_street_id", type="integer")
     */
    private $geoStreetId;

    /**
     * @var string
     *
     * @ORM\Column(name="house", type="string", length=10, nullable=true)
     */
    private $house;

    /**
     * @var string
     *
     * @ORM\Column(name="building", type="string", length=10, nullable=true)
     */
    private $building;

    /**
     * @var string
     *
     * @ORM\Column(name="apartment", type="string", length=10, nullable=true)
     */
    private $apartment;

    /**
     * @var int
     *
     * @ORM\Column(name="floor", type="integer", nullable=true)
     */
    private $floor;

    /**
     * @var bool
     *
     * @ORM\Column(name="has_lift", type="boolean", nullable=true)
     */
    private $hasLift;

    /**
     * @var int
     *
     * @ORM\Column(name="office", type="integer", nullable=true)
     */
    private $office;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_subway_station_id", type="integer", nullable=true)
     */
    private $geoSubwayStationId;

    /**
     * @var string
     *
     * @ORM\Column(name="coordinates", type="string", length=0, nullable=true)
     */
    private $coordinates;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=0, nullable=true)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=0, nullable=true)
     */
    private $address;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set geoStreetId
     *
     * @param integer $geoStreetId
     *
     * @return GeoAddress
     */
    public function setGeoStreetId($geoStreetId)
    {
        $this->geoStreetId = $geoStreetId;

        return $this;
    }

    /**
     * Get geoStreetId
     *
     * @return int
     */
    public function getGeoStreetId()
    {
        return $this->geoStreetId;
    }

    /**
     * Set house
     *
     * @param string $house
     *
     * @return GeoAddress
     */
    public function setHouse($house)
    {
        $this->house = $house;

        return $this;
    }

    /**
     * Get house
     *
     * @return string
     */
    public function getHouse()
    {
        return $this->house;
    }

    /**
     * Set building
     *
     * @param string $building
     *
     * @return GeoAddress
     */
    public function setBuilding($building)
    {
        $this->building = $building;

        return $this;
    }

    /**
     * Get building
     *
     * @return string
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * Set apartment
     *
     * @param string $apartment
     *
     * @return GeoAddress
     */
    public function setApartment($apartment)
    {
        $this->apartment = $apartment;

        return $this;
    }

    /**
     * Get apartment
     *
     * @return string
     */
    public function getApartment()
    {
        return $this->apartment;
    }

    /**
     * Set floor
     *
     * @param integer $floor
     *
     * @return GeoAddress
     */
    public function setFloor($floor)
    {
        $this->floor = $floor;

        return $this;
    }

    /**
     * Get floor
     *
     * @return int
     */
    public function getFloor()
    {
        return $this->floor;
    }

    /**
     * Set hasLift
     *
     * @param boolean $hasLift
     *
     * @return GeoAddress
     */
    public function setHasLift($hasLift)
    {
        $this->hasLift = $hasLift;

        return $this;
    }

    /**
     * Get hasLift
     *
     * @return bool
     */
    public function getHasLift()
    {
        return $this->hasLift;
    }

    /**
     * Set office
     *
     * @param integer $office
     *
     * @return GeoAddress
     */
    public function setOffice($office)
    {
        $this->office = $office;

        return $this;
    }

    /**
     * Get office
     *
     * @return int
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * Set geoSubwayStationId
     *
     * @param integer $geoSubwayStationId
     *
     * @return GeoAddress
     */
    public function setGeoSubwayStationId($geoSubwayStationId)
    {
        $this->geoSubwayStationId = $geoSubwayStationId;

        return $this;
    }

    /**
     * Get geoSubwayStationId
     *
     * @return int
     */
    public function getGeoSubwayStationId()
    {
        return $this->geoSubwayStationId;
    }

    /**
     * Set coordinates
     *
     * @param string $coordinates
     *
     * @return GeoAddress
     */
    public function setCoordinates($coordinates)
    {
        $this->coordinates = $coordinates;

        return $this;
    }

    /**
     * Get coordinates
     *
     * @return string
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return GeoAddress
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return GeoAddress
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }
}
