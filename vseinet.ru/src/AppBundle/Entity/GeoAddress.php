<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Doctrine\DBAL\ValueObject\Point;

/**
 * GeoAddress
 *
 * @ORM\Table(name="geo_address")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GeoAddressRepository")
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
     * @var string
     *
     * @ORM\Column(name="postal_code", type="string")
     */
    private $postalCode;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_region_id", type="integer")
     */
    private $geoRegionId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_area_id", type="integer")
     */
    private $geoAreaId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer")
     */
    private $geoCityId;

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
     * @var Point
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
     * Set postalCode.
     *
     * @param string $postalCode
     *
     * @return GeoAddress
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode.
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set geoRegionId.
     *
     * @param int $geoRegionId
     *
     * @return GeoAddress
     */
    public function setGeoRegionId($geoRegionId)
    {
        $this->geoRegionId = $geoRegionId;

        return $this;
    }

    /**
     * Get geoRegionId.
     *
     * @return int
     */
    public function getGeoRegionId()
    {
        return $this->geoRegionId;
    }

    /**
     * Set geoAreaId.
     *
     * @param int $geoAreaId
     *
     * @return GeoAddress
     */
    public function setGeoAreaId($geoAreaId)
    {
        $this->geoAreaId = $geoAreaId;

        return $this;
    }

    /**
     * Get geoAreaId.
     *
     * @return int
     */
    public function getGeoAreaId()
    {
        return $this->geoAreaId;
    }

    /**
     * Set geoCityId.
     *
     * @param int $geoCityId
     *
     * @return GeoAddress
     */
    public function setGeoCityId($geoCityId)
    {
        $this->geoCityId = $geoCityId;

        return $this;
    }

    /**
     * Get geoCityId.
     *
     * @return int
     */
    public function getGeoCityId()
    {
        return $this->geoCityId;
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
     * Set coordinates.
     *
     * @param Point $coordinates
     *
     * @return GeoAddress
     */
    public function setCoordinates(?Point $coordinates)
    {
        $this->coordinates = $coordinates;

        return $this;
    }

    /**
     * Get coordinates.
     *
     * @return Point
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
