<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoRegion
 *
 * @ORM\Table(name="geo_region")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GeoRegionRepository")
 */
class GeoRegion
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
     * @ORM\Column(name="name", type="string")
     */
    private $name;

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
     * @return GeoRegion
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
     * Set AOGUID.
     *
     * @param string $AOGUID
     *
     * @return GeoRegion
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
     * @return GeoRegion
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
}
