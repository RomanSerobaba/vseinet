<?php

namespace PricingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Competitor
 *
 * @ORM\Table(name="competitor")
 * @ORM\Entity(repositoryClass="PricingBundle\Repository\CompetitorRepository")
 */
class Competitor
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
     * @ORM\Column(name="link", type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255)
     */
    private $alias;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_utf", type="boolean")
     */
    private $isUtf;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var int
     * 
     * @ORM\Column(name="supplier_id", type="integer")
     */
    private $supplierId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer")
     */
    private $geoCityId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_address_id", type="integer")
     */
    private $geoAddressId;

    /**
     * @var string
     *
     * @ORM\Column(name="channel", type="string", length=255)
     */
    private $channel;

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
     * @return Competitor
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
     * Set link
     *
     * @param string $link
     *
     * @return Competitor
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set alias
     *
     * @param string $alias
     *
     * @return Competitor
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set isUtf
     *
     * @param boolean $isUtf
     *
     * @return Competitor
     */
    public function setIsUtf($isUtf = null)
    {
        $this->isUtf = $isUtf;

        return $this;
    }

    /**
     * Get isUtf
     *
     * @return bool
     */
    public function getIsUtf()
    {
        return $this->isUtf;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Competitor
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set supplierId
     *
     * @param integer $supplierId
     *
     * @return Competitor
     */
    public function setSupplierId($supplierId = null)
    {
        $this->supplierId = $supplierId;

        return $this;
    }

    /**
     * Get supplierId
     *
     * @return int
     */
    public function getSupplierId()
    {
        return $this->supplierId;
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
    public function setGeoCityId($geoCityId = null)
    {
        $this->geoCityId = $geoCityId;
    }

    /**
     * @return int
     */
    public function getGeoAddressId(): int
    {
        return $this->geoAddressId;
    }

    /**
     * @param int $geoAddressId
     */
    public function setGeoAddressId($geoAddressId = null)
    {
        $this->geoAddressId = $geoAddressId;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @param string $channel
     */
    public function setChannel(string $channel)
    {
        $this->channel = $channel;
    }

}