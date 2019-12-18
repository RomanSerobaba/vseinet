<?php

namespace AppBundle\Entity;

use AppBundle\Doctrine\DBAL\ValueObject\Point;
use Doctrine\ORM\Mapping as ORM;

/**
 * GeoIp.
 *
 * @ORM\Table(name="geo_ip_city")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GeoIpCityRepository")
 */
class GeoIpCity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_country_id", type="integer", nullable=true)
     */
    private $geoCountryId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_region_id", type="integer", nullable=true)
     */
    private $geoRegionId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer", nullable=true)
     */
    private $geoCityId;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string")
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string")
     */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(name="district", type="string")
     */
    private $district;

    /**
     * @var Point
     *
     * @ORM\Column(name="coordinates", type="point", nullable=true)
     */
    private $coordinates;

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return GeoIpCity
     */
    public function setId($id): GeoIpCity
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set geoCountryId.
     *
     * @param int $geoCountryId
     *
     * @return GeoIpCity
     */
    public function setGeoCountryId($geoCountryId): GeoIpCity
    {
        $this->geoCountryId = $geoCountryId;

        return $this;
    }

    /**
     * Get geoCountryId.
     *
     * @return int|null
     */
    public function getGeoCountryId(): ?int
    {
        return $this->geoCountryId;
    }

    /**
     * Set geoCityId.
     *
     * @param int $geoCityId
     *
     * @return GeoIpCity
     */
    public function setGeoCityId($geoCityId): GeoIpCity
    {
        $this->geoCityId = $geoCityId;

        return $this;
    }

    /**
     * Get geoCityId.
     *
     * @return int|null
     */
    public function getGeoCityId(): ?int
    {
        return $this->geoCityId;
    }

    /**
     * Set geoRegionId.
     *
     * @param int $geoRegionId
     *
     * @return GeoIpCity
     */
    public function setGeoRegionId($geoRegionId): GeoIpCity
    {
        $this->geoRegionId = $geoRegionId;

        return $this;
    }

    /**
     * Get geoRegionId.
     *
     * @return int|null
     */
    public function getGeoRegionId(): ?int
    {
        return $this->geoRegionId;
    }

    /**
     * Set city.
     *
     * @param string $city
     *
     * @return GeoIpCity
     */
    public function setCity($city): GeoIpCity
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * Set region.
     *
     * @param string $region
     *
     * @return GeoIpCity
     */
    public function setRegion($region): GeoIpCity
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region.
     *
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * Set district.
     *
     * @param string $district
     *
     * @return GeoIpCity
     */
    public function setDistrict($district): GeoIpCity
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get district.
     *
     * @return string
     */
    public function getDistrict(): string
    {
        return $this->district;
    }

    /**
     * Set coordinates.
     *
     * @param Point $coordinates
     *
     * @return GeoIpCity
     */
    public function setCoordinates(Point $coordinates): GeoIpCity
    {
        $this->coordinates = $coordinates;

        return $this;
    }

    /**
     * Get coordinates.
     *
     * @return Point|null
     */
    public function getCoordinates(): ?Point
    {
        return $this->coordinates;
    }
}
