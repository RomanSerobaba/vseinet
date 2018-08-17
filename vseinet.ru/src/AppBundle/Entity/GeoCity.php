<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoCity
 *
 * @ORM\Table(name="geo_city")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GeoCityRepository")
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
     * @var array<GeoPoint>
     */
    private $geoPoints;


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

    /**
     * Set geoPoints
     *
     * @param int $geoPoints
     *
     * @return GeoCity
     */
    public function setGeoPoints($geoPoints): self
    {
        $this->geoPoints = $geoPoints;

        return $this;
    }

    /**
     * Get geoPoints
     *
     * @return int
     */
    public function getGeoPoints(): array
    {
        return $this->geoPoints ?? [];
    }

    /**
     * Get count geoPoints
     *
     * @return int
     */
    public function getCountGeoPoints(): int
    {
        return count($this->getGeoPoints());
    }

    /**
     * Get count new geoPoints
     *
     * @return int
     */
    public function getCountNewGeoPoints(): int 
    {
        $count = 0;
        $opening = new \DateTime('-2 month');
        foreach ($this->getGeoPoints() as $geoPoint) {
            if ($geoPoint->getOpeningDate() && $opening < $geoPoint->getOpeningDate()) {
                $count += 1;
            }
        }

        return $count;
    }

    /**
     * Get has retail
     *
     * @return bool
     */
    public function getHasRetail(): bool 
    {
        foreach ($this->getGeoPoints() as $geoPoint) {
            if ($geoPoint->getHasRetail()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get has delivery
     *
     * @return bool
     */
    public function getHasDelivery(): bool
    {
        foreach ($this->getGeoPoints() as $geoPoint) {
            if ($geoPoint->getHasDelivery()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get realId
     * 
     * @return int
     */
    public function getRealId(): int 
    {
        return $this->getCountGeoPoints() ? $this->id : 0;
    }
}
