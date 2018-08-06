<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ViewGeoPoint
 *
 * @ORM\Table(name="view_geo_point")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\ViewGeoPointRepository")
 */
class ViewGeoPoint
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer")
     */
    private $geoCityId;

    /**
     * @var string
     *
     * @ORM\Column(name="geo_city", type="string", length=255)
     */
    private $geoCity;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_region_id", type="integer")
     */
    private $geoRegionId;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_central_city", type="boolean")
     */
    private $isCentralCity;


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
     * Set name.
     *
     * @param string $name
     *
     * @return ViewGeoPoint
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return ViewGeoPoint
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set code.
     *
     * @param string $code
     *
     * @return ViewGeoPoint
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set geoCityId.
     *
     * @param int $geoCityId
     *
     * @return ViewGeoPoint
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
     * Set geoCity.
     *
     * @param string $geoCity
     *
     * @return ViewGeoPoint
     */
    public function setGeoCity($geoCity)
    {
        $this->geoCity = $geoCity;

        return $this;
    }

    /**
     * Get geoCity.
     *
     * @return string
     */
    public function getGeoCity()
    {
        return $this->geoCity;
    }

    /**
     * Set geoRegionId.
     *
     * @param int $geoRegionId
     *
     * @return ViewGeoPoint
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
     * Set isCentralCity.
     *
     * @param bool $isCentralCity
     *
     * @return ViewGeoPoint
     */
    public function setIsCentralCity($isCentralCity)
    {
        $this->isCentralCity = $isCentralCity;

        return $this;
    }

    /**
     * Get isCentralCity.
     *
     * @return bool
     */
    public function getIsCentralCity()
    {
        return $this->isCentralCity;
    }
}
