<?php

namespace ThirdPartyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoStreet
 *
 * @ORM\Table(name="geo_street")
 * @ORM\Entity(repositoryClass="ThirdPartyBundle\Repository\GeoStreetRepository")
 */
class GeoStreet
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
     * @ORM\Column(name="unit", type="string", length=36)
     */
    private $unit;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer", nullable=true)
     */
    private $geoCityId;

    /**
     * @var int
     *
     * @ORM\Column(name="`AOGUID`", type="string", length=36)
     */
    private $AOGUID;

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
     * @return GeoStreet
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
     * Set geo_city_id
     *
     * @param integer $geoCityId
     *
     * @return GeoStreet
     */
    public function setGeoCityId($geoCityId)
    {
        $this->geoCityId = $geoCityId;

        return $this;
    }

    /**
     * Get geo_city_id
     *
     * @return int
     */
    public function getGeoCityId()
    {
        return $this->geoCityId;
    }

    /**
     * Set AOGUID
     *
     * @param string $AOGUID
     *
     * @return GeoStreet
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

