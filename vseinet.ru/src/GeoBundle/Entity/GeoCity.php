<?php

namespace GeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoCity
 *
 * @ORM\Table(name="geo_city")
 * @ORM\Entity(repositoryClass="GeoBundle\Repository\GeoCityRepository")
 */
class GeoCity
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
     * @ORM\Column(name="geo_region_id", type="integer")
     */
    private $geoRegionId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_central", type="boolean")
     */
    private $isCentral;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_code", type="string")
     */
    private $phoneCode;

    /**
     * @var string
     *
     * @ORM\Column(name="""AOGUID""", type="string")
     */
    private $AOGUID;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string")
     */
    private $unit;

    /**
     * @var integer
     *
     * @ORM\Column(name="""AOLEVEL""", type="integer")
     */
    private $AOLEVEL;


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
     * Set geoRegionId.
     *
     * @param int $geoRegionId
     *
     * @return GeoCity
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
     * Set name.
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
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set isCentral.
     *
     * @param bool $isCentral
     *
     * @return GeoCity
     */
    public function setIsCentral($isCentral)
    {
        $this->isCentral = $isCentral;

        return $this;
    }

    /**
     * Get isCentral.
     *
     * @return bool
     */
    public function getIsCentral()
    {
        return $this->isCentral;
    }

    /**
     * Set phoneCode.
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
     * Get phoneCode.
     *
     * @return string
     */
    public function getPhoneCode()
    {
        return $this->phoneCode;
    }

    /**
     * Set AOGUID.
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
     * Get AOGUID.
     *
     * @return string
     */
    public function getAOGUID()
    {
        return $this->AOGUID;
    }

    /**
     * Set unit.
     *
     * @param string $unit
     *
     * @return GeoCity
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit.
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set AOLEVEL.
     *
     * @param string $AOLEVEL
     *
     * @return GeoCity
     */
    public function setAOLEVEL($AOLEVEL)
    {
        $this->AOLEVEL = $AOLEVEL;

        return $this;
    }

    /**
     * Get AOLEVEL.
     *
     * @return string
     */
    public function getAOLEVEL()
    {
        return $this->AOLEVEL;
    }
}
