<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoRoom
 *
 * @ORM\Table(name="geo_room")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GeoRoomRepository")
 */
class GeoRoom
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
     * @ORM\Column(name="geo_point_id", type="integer")
     */
    private $geoPointId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=10)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=10)
     */
    private $type;

    /**
     * @var bool
     *
     * @ORM\Column(name="has_auto_release", type="boolean")
     */
    private $hasAutoRelease;

    /**
     * @var int
     *
     * @ORM\Column(name="write_off_order", type="integer")
     */
    private $writeOffOrder;


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
     * Set geoPointId.
     *
     * @param int $geoPointId
     *
     * @return GeoRoom
     */
    public function setGeoPointId($geoPointId)
    {
        $this->geoPointId = $geoPointId;

        return $this;
    }

    /**
     * Get geoPointId.
     *
     * @return int
     */
    public function getGeoPointId()
    {
        return $this->geoPointId;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return GeoRoom
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
     * Set code.
     *
     * @param string $code
     *
     * @return GeoRoom
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
     * Set type.
     *
     * @param string $type
     *
     * @return GeoRoom
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
     * Set hasAutoRelease.
     *
     * @param bool $hasAutoRelease
     *
     * @return GeoRoom
     */
    public function setHasAutoRelease($hasAutoRelease)
    {
        $this->hasAutoRelease = $hasAutoRelease;

        return $this;
    }

    /**
     * Get hasAutoRelease.
     *
     * @return bool
     */
    public function getHasAutoRelease()
    {
        return $this->hasAutoRelease;
    }

    /**
     * Set writeOffOrder.
     *
     * @param int $writeOffOrder
     *
     * @return GeoRoom
     */
    public function setWriteOffOrder($writeOffOrder)
    {
        $this->writeOffOrder = $writeOffOrder;

        return $this;
    }

    /**
     * Get writeOffOrder.
     *
     * @return int
     */
    public function getWriteOffOrder()
    {
        return $this->writeOffOrder;
    }
}
