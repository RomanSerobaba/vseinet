<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoIp.
 *
 * @ORM\Table(name="geo_ip")
 * @ORM\Entity(repositoryClass="GeoBundle\Repository\GeoIpRepository")
 */
class GeoIp
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
     * @ORM\Column(name="geo_country_id", type="integer", nullable=true)
     */
    private $geoCountryId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_ip_city_id", type="integer", nullable=true)
     */
    private $geoIpCityId;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_range", type="int8range")
     */
    private $ipRange;

    /**
     * @var string
     *
     * @ORM\Column(name="ip1", type="string")
     */
    private $ip1;

    /**
     * @var string
     *
     * @ORM\Column(name="ip2", type="string")
     */
    private $ip2;

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
     * @return GeoIp
     */
    public function setGeoCountryId($geoCountryId): GeoIp
    {
        $this->geoCountryId = $geoCountryId ?: null;

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
     * Set geoIpCityId.
     *
     * @param int $geoIpCityId
     *
     * @return GeoIp
     */
    public function setGeoIpCityId($geoIpCityId): GeoIp
    {
        $this->geoIpCityId = $geoIpCityId > 0 ? $geoIpCityId : null;

        return $this;
    }

    /**
     * Get geoIpCityId.
     *
     * @return int|null
     */
    public function getGeoIpCityId(): ?int
    {
        return $this->geoIpCityId;
    }

    /**
     * Set ipRange.
     *
     * @param string $ipRange
     *
     * @return GeoIp
     */
    public function setIpRange($ipRange): GeoIp
    {
        $this->ipRange = $ipRange;

        return $this;
    }

    /**
     * Get ipRange.
     *
     * @return string
     */
    public function getIpRange(): string
    {
        return $this->ipRange;
    }

    /**
     * Set ip1.
     *
     * @param string $ip1
     *
     * @return GeoIp
     */
    public function setIp1($ip1): GeoIp
    {
        $this->ip1 = $ip1;

        return $this;
    }

    /**
     * Get ip1.
     *
     * @return string
     */
    public function getIp1(): string
    {
        return $this->ip1;
    }

    /**
     * Set ip2.
     *
     * @param string $ip2
     *
     * @return GeoIp
     */
    public function setIp2($ip2): GeoIp
    {
        $this->ip2 = $ip2;

        return $this;
    }

    /**
     * Get ip2.
     *
     * @return string
     */
    public function getIp2(): string
    {
        return $this->ip2;
    }
}
