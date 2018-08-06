<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoRoom
 *
 * @ORM\Table(name="geo_room")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\GeoRoomRepository")
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
     * @ORM\Column(name="geo_point_id", type="integer", nullable=true)
     */
    private $geoPointId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=5, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=128, nullable=true)
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_default", type="boolean", nullable=true)
     */
    private $isDefault;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_auto_release", type="boolean", nullable=true)
     */
    private $hasAutoRelease;

    /**
     * @var int
     *
     * @ORM\Column(name="write_off_order", type="integer", nullable=true)
     */
    private $writeOffOrder;


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
     * Set type
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
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
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
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set geoPointId
     *
     * @param integer $geoPointId
     *
     * @return GeoRoom
     */
    public function setGeoPointId($geoPointId)
    {
        $this->geoPointId = $geoPointId;

        return $this;
    }

    /**
     * Get geoPointId
     *
     * @return int
     */
    public function getGeoPointId()
    {
        return $this->geoPointId;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * @param bool $isDefault
     */
    public function setIsDefault(bool $isDefault)
    {
        $this->isDefault = $isDefault;
    }

    /**
     * @return bool
     */
    public function isHasAutoRelease(): bool
    {
        return $this->hasAutoRelease;
    }

    /**
     * @param bool $hasAutoRelease
     */
    public function setHasAutoRelease(bool $hasAutoRelease)
    {
        $this->hasAutoRelease = $hasAutoRelease;
    }

    /**
     * @return int
     */
    public function getWriteOffOrder(): int
    {
        return $this->writeOffOrder;
    }

    /**
     * @param int $writeOffOrder
     */
    public function setWriteOffOrder(int $writeOffOrder)
    {
        $this->writeOffOrder = $writeOffOrder;
    }
}

