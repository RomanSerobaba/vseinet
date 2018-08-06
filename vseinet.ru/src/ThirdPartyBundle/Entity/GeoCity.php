<?php

namespace ThirdPartyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoCity
 *
 * @ORM\Table(name="geo_city")
 * @ORM\Entity(repositoryClass="ThirdPartyBundle\Repository\GeoCityRepository")
 */
class GeoCity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_region_id", type="integer")
     */
    private $geoRegionId;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_central", type="boolean")
     */
    private $isCentral;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_code", type="string", length=5, nullable=true)
     */
    private $phoneCode;


    /**
     * @var string
     *
     * @ORM\Column(name="`AOGUID`", type="string", length=36)
     */
    private $AOGUID;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string", length=36)
     */
    private $unit;

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

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
     * Set name
     *
     * @param string $name
     *
     * @return GeoCity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set geoRegionId
     *
     * @param integer $geoRegionId
     *
     * @return GeoCity
     */
    public function setGeoRegionId($geoRegionId)
    {
        $this->geoRegionId = $geoRegionId;

        return $this;
    }

    /**
     * Get geoRegionId
     *
     * @return int
     */
    public function getGeoRegionId()
    {
        return $this->geoRegionId;
    }

    /**
     * Set isCentral
     *
     * @param boolean $isCentral
     *
     * @return GeoCity
     */
    public function setIsCentral($isCentral)
    {
        $this->isCentral = $isCentral;

        return $this;
    }

    /**
     * Get isCentral
     *
     * @return bool
     */
    public function getIsCentral()
    {
        return $this->isCentral;
    }

    /**
     * Set phoneCode
     *
     * @param string $phoneCode
     *
     * @return GeoCity
     */
    public function setPhoneCode($phoneCode)
    {
        $this->phoneCode = $phoneCode;

        return $this;
    }

    /**
     * Get phoneCode
     *
     * @return string
     */
    public function getPhoneCode()
    {
        return $this->phoneCode;
    }

    /**
     * Set AOGUID
     *
     * @param string $AOGUID
     *
     * @return GeoCity
     */
    public function setAOGUID($AOGUID)
    {
        $this->AOGUID = $AOGUID;

        return $this;
    }

    /**
     * Get AOGUID
     *
     * @return string
     */
    public function getAOGUID()
    {
        return $this->AOGUID;
    }

    /**
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     */
    public function setUnit(string $unit)
    {
        $this->unit = $unit;
    }
}

