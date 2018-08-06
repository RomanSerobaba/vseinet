<?php

namespace ClaimsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceCenter
 *
 * @ORM\Table(name="service_center")
 * @ORM\Entity(repositoryClass="ClaimsBundle\Repository\ServiceCenterRepository")
 */
class ServiceCenter
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
     * @var int|null
     *
     * @ORM\Column(name="geo_point_id", type="integer", nullable=true)
     */
    private $geoPointId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int|null
     *
     * @ORM\Column(name="supplier_id", type="integer", nullable=true)
     */
    private $supplierId;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;


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
     * @param int|null $geoPointId
     *
     * @return ServiceCenter
     */
    public function setGeoPointId($geoPointId = null)
    {
        $this->geoPointId = $geoPointId;

        return $this;
    }

    /**
     * Get geoPointId.
     *
     * @return int|null
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
     * @return ServiceCenter
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
     * Set supplierId.
     *
     * @param int|null $supplierId
     *
     * @return ServiceCenter
     */
    public function setSupplierId($supplierId = null)
    {
        $this->supplierId = $supplierId;

        return $this;
    }

    /**
     * Get supplierId.
     *
     * @return int|null
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return ServiceCenter
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}
