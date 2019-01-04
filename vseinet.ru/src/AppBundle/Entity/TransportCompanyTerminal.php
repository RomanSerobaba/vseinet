<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TransportCompanyTerminal
 *
 * @ORM\Table(name="transport_company_terminal")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TransportCompanyTerminalRepository")
 */
class TransportCompanyTerminal
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
     * @ORM\Column(name="native_id", type="string", length=255, nullable=true)
     */
    private $nativeId;

    /**
     * @var string
     *
     * @ORM\Column(name="native_name", type="string", length=255)
     */
    private $nativeName;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer")
     */
    private $geoCityId;

    /**
     * @var int
     *
     * @ORM\Column(name="transport_company_id", type="integer")
     */
    private $transportCompanyId;

    /**
     * @var string
     *
     * @ORM\Column(name="native_link", type="string", length=255, nullable=true)
     */
    private $nativeLink;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var bool
     *
     * @ORM\Column(name="tmp_active", type="boolean", nullable=true)
     */
    private $tmpActive;


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
     * Set nativeId
     *
     * @param string $nativeId
     *
     * @return TransportCompanyTerminal
     */
    public function setNativeId($nativeId)
    {
        $this->nativeId = $nativeId;

        return $this;
    }

    /**
     * Get nativeId
     *
     * @return string
     */
    public function getNativeId()
    {
        return $this->nativeId;
    }

    /**
     * Set nativeName
     *
     * @param string $nativeName
     *
     * @return TransportCompanyTerminal
     */
    public function setNativeName($nativeName)
    {
        $this->nativeName = $nativeName;

        return $this;
    }

    /**
     * Get nativeName
     *
     * @return string
     */
    public function getNativeName()
    {
        return $this->nativeName;
    }

    /**
     * Set geoCityId
     *
     * @param integer $geoCityId
     *
     * @return TransportCompanyTerminal
     */
    public function setGeoCityId($geoCityId)
    {
        $this->geoCityId = $geoCityId;

        return $this;
    }

    /**
     * Get geoCityId
     *
     * @return int
     */
    public function getGeoCityId()
    {
        return $this->geoCityId;
    }

    /**
     * Set transportCompanyId
     *
     * @param integer $transportCompanyId
     *
     * @return TransportCompanyTerminal
     */
    public function setTransportCompanyId($transportCompanyId)
    {
        $this->transportCompanyId = $transportCompanyId;

        return $this;
    }

    /**
     * Get transportCompanyId
     *
     * @return int
     */
    public function getTransportCompanyId()
    {
        return $this->transportCompanyId;
    }

    /**
     * Set nativeLink
     *
     * @param string $nativeLink
     *
     * @return TransportCompanyTerminal
     */
    public function setNativeLink($nativeLink)
    {
        $this->nativeLink = $nativeLink;

        return $this;
    }

    /**
     * Get nativeLink
     *
     * @return string
     */
    public function getNativeLink()
    {
        return $this->nativeLink;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return TransportCompanyTerminal
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
     * Set tmpActive
     *
     * @param boolean $tmpActive
     *
     * @return TransportCompanyTerminal
     */
    public function setTmpActive($tmpActive)
    {
        $this->tmpActive = $tmpActive;

        return $this;
    }

    /**
     * Get tmpActive
     *
     * @return bool
     */
    public function getTmpActive()
    {
        return $this->tmpActive;
    }
}

