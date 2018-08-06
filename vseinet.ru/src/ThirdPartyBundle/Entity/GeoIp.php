<?php

namespace ThirdPartyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoIp
 *
 * @ORM\Table(name="geo_ip")
 * @ORM\Entity(repositoryClass="ThirdPartyBundle\Repository\GeoBaseRepository")
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
     * @ORM\Column(name="long_ip1", type="integer")
     */
    private $longIp1;

    /**
     * @var int
     *
     * @ORM\Column(name="long_ip2", type="integer")
     */
    private $longIp2;

    /**
     * @var string
     *
     * @ORM\Column(name="ip1", type="string", length=16)
     */
    private $ip1;

    /**
     * @var string
     *
     * @ORM\Column(name="ip2", type="string", length=16)
     */
    private $ip2;

    /**
     * @var int
     *
     * @ORM\Column(name="city_id", type="integer")
     */
    private $cityId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer")
     */
    private $geoCityId;

    /**
     * Set longIp1.
     *
     * @param int $longIp1
     *
     * @return GeoIp
     */
    public function setLongIp1($longIp1)
    {
        $this->longIp1 = $longIp1;

        return $this;
    }

    /**
     * Get longIp1.
     *
     * @return int
     */
    public function getLongIp1()
    {
        return $this->longIp1;
    }

    /**
     * Set longIp2.
     *
     * @param int $longIp2
     *
     * @return GeoIp
     */
    public function setLongIp2($longIp2)
    {
        $this->longIp2 = $longIp2;

        return $this;
    }

    /**
     * Get longIp2.
     *
     * @return int
     */
    public function getLongIp2()
    {
        return $this->longIp2;
    }

    /**
     * Set ip1.
     *
     * @param string $ip1
     *
     * @return GeoIp
     */
    public function setIp1($ip1)
    {
        $this->ip1 = $ip1;

        return $this;
    }

    /**
     * Get ip1.
     *
     * @return string
     */
    public function getIp1()
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
    public function setIp2($ip2)
    {
        $this->ip2 = $ip2;

        return $this;
    }

    /**
     * Get ip2.
     *
     * @return string
     */
    public function getIp2()
    {
        return $this->ip2;
    }

    /**
     * Set cityId.
     *
     * @param int $cityId
     *
     * @return GeoIp
     */
    public function setCityId($cityId)
    {
        $this->cityId = $cityId;

        return $this;
    }

    /**
     * Get cityId.
     *
     * @return int
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * @return int
     */
    public function getGeoCityId(): int
    {
        return $this->geoCityId;
    }

    /**
     * @param int $geoCityId
     */
    public function setGeoCityId(int $geoCityId)
    {
        $this->geoCityId = $geoCityId;
    }
}
