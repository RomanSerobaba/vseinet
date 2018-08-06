<?php

namespace AccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OurSeller
 *
 * @ORM\Table(name="our_seller")
 * @ORM\Entity(repositoryClass="AccountingBundle\Repository\OurSellerRepository")
 */
class OurSeller
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="counteragent_id", type="integer")
     */
    private $counteragentId;

    /**
     * @var string
     *
     * @ORM\Column(name="short_name", type="string", length=255)
     */
    private $shortName;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_legal", type="boolean")
     */
    private $isLegal;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_natural", type="boolean")
     */
    private $isNatural;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_default", type="boolean")
     */
    private $isDefault;

    /**
     * Set counteragentId
     *
     * @param integer $counteragentId
     *
     * @return OurSeller
     */
    public function setCounteragentId($counteragentId)
    {
        $this->counteragentId = $counteragentId;

        return $this;
    }

    /**
     * Get counteragentId
     *
     * @return int
     */
    public function getCounteragentId()
    {
        return $this->counteragentId;
    }

    /**
     * Set shortName
     *
     * @param string $shortName
     *
     * @return OurSeller
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Set isLegal
     *
     * @param boolean $isLegal
     *
     * @return OurSeller
     */
    public function setIsLegal($isLegal)
    {
        $this->isLegal = $isLegal;

        return $this;
    }

    /**
     * Get isLegal
     *
     * @return bool
     */
    public function getIsLegal()
    {
        return $this->isLegal;
    }

    /**
     * Set isNatural
     *
     * @param boolean $isNatural
     *
     * @return OurSeller
     */
    public function setIsNatural($isNatural)
    {
        $this->isNatural = $isNatural;

        return $this;
    }

    /**
     * Get isNatural
     *
     * @return bool
     */
    public function getIsNatural()
    {
        return $this->isNatural;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return OurSeller
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
     * Set isDefault
     *
     * @param boolean $isDefault
     *
     * @return OurSeller
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * Get isDefault
     *
     * @return bool
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }
}

