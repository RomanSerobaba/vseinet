<?php

namespace GeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoAddress
 *
 * @ORM\Table(name="geo_address")
 * @ORM\Entity(repositoryClass="GeoBundle\Repository\GeoAddressRepository")
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
     * @ORM\Column(name="house", type="string")
     */
    private $house;

    /**
     * @var string
     *
     * @ORM\Column(name="building", type="string")
     */
    private $building;

    /**
     * @var string
     *
     * @ORM\Column(name="apartment", type="string")
     */
    private $apartment;

    /**
     * @var int
     *
     * @ORM\Column(name="floor", type="integer")
     */
    private $floor;

    /**
     * @var bool
     *
     * @ORM\Column(name="has_lift", type="boolean")
     */
    private $hasLift;

    /**
     * @var string
     *
     * @ORM\Column(name="office", type="string")
     */
    private $office;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_subway_station_id", type="integer")
     */
    private $geoSubwayStationId;

    /**
     * @var \AppBundle\Doctrine\DBAL\ValueObject\Point
     *
     * @ORM\Column(name="coordinates", type="point")
     */
    private $coordinates;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string")
     */
    private $address;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set geoStreetId.
     *
     * @param int $geoStreetId
     *
     * @return GeoAddress
     */
    public function setGeoStreetId($geoStreetId)
    {
        $this->geoStreetId = $geoStreetId;

        return $this;
    }

    /**
     * Get geoStreetId.
     *
     * @return int
     */
    public function getGeoStreetId()
    {
        return $this->geoStreetId;
    }

    /**
     * Set house.
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
     * Get house.
     *
     * @return string
     */
    public function getHouse()
    {
        return $this->house;
    }

    /**
     * Set building.
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
     * Get building.
     *
     * @return string
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * Set apartment.
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
     * Get apartment.
     *
     * @return string
     */
    public function getApartment()
    {
        return $this->apartment;
    }

    /**
     * Set floor.
     *
     * @param int $floor
     *
     * @return GeoAddress
     */
    public function setFloor($floor)
    {
        $this->floor = $floor;

        return $this;
    }

    /**
     * Get floor.
     *
     * @return int
     */
    public function getFloor()
    {
        return $this->floor;
    }

    /**
     * Set hasLift.
     *
     * @param bool $hasLift
     *
     * @return GeoAddress
     */
    public function setHasLift($hasLift)
    {
        $this->hasLift = $hasLift;

        return $this;
    }

    /**
     * Get hasLift.
     *
     * @return bool
     */
    public function getHasLift()
    {
        return $this->hasLift;
    }

    /**
     * Set office.
     *
     * @param string $office
     *
     * @return GeoAddress
     */
    public function setOffice($office)
    {
        $this->office = $office;

        return $this;
    }

    /**
     * Get office.
     *
     * @return string
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * Set geoSubwayStationId.
     *
     * @param int $geoSubwayStationId
     *
     * @return GeoAddress
     */
    public function setGeoSubwayStationId($geoSubwayStationId)
    {
        $this->geoSubwayStationId = $geoSubwayStationId;

        return $this;
    }

    /**
     * Get geoSubwayStationId.
     *
     * @return int
     */
    public function getGeoSubwayStationId()
    {
        return $this->geoSubwayStationId;
    }

    /**
     * Set coordinates.
     *
     * @param @param \AppBundle\Doctrine\DBAL\ValueObject\Point $coordinates
     *
     * @return GeoAddress
     */
    public function setCoordinates(\AppBundle\Doctrine\DBAL\ValueObject\Point $coordinates)
    {
        $this->coordinates = $coordinates;

        return $this;
    }

    /**
     * Get coordinates.
     *
     * @return \AppBundle\Doctrine\DBAL\ValueObject\Point
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * Set comment.
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
     * Get comment.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set address.
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
     * Get address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }
}
