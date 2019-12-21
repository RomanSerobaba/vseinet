<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Brand.
 *
 * @ORM\Table(name="brand")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BrandRepository")\
 */
class Brand
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
     * @ORM\Column(name="sef_name", type="string")
     */
    private $sefName;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string")
     */
    private $logo;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string")
     */
    private $url;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_forbidden", type="boolean")
     */
    private $isForbidden;

    /**
     * @var int
     *
     * @ORM\Column(name="canonical_id", type="integer", nullable=true)
     */
    private $canonicalId;

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
     * @return Brand
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
     * Set logo.
     *
     * @param string $logo
     *
     * @return Brand
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo.
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set url.
     *
     * @param string url
     *
     * @return Brand
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set isForbidden.
     *
     * @param bool $isForbidden
     *
     * @return Brand
     */
    public function setIsForbidden($isForbidden)
    {
        $this->isForbidden = $isForbidden;

        return $this;
    }

    /**
     * Get isForbidden.
     *
     * @return bool
     */
    public function getIsForbidden()
    {
        return $this->isForbidden;
    }

    /**
     * Set canonicalId.
     *
     * @param int $canonicalId
     *
     * @return Brand
     */
    public function setCanonicalId($canonicalId)
    {
        $this->canonicalId = $canonicalId;

        return $this;
    }

    /**
     * Get canonicalId.
     *
     * @return int
     */
    public function getCanonicalId()
    {
        return $this->canonicalId;
    }

    /**
     * Set sefName.
     *
     * @param string $sefName
     *
     * @return Brand
     */
    public function setSefName($sefName)
    {
        $this->sefName = $sefName;

        return $this;
    }

    /**
     * Get sefName.
     *
     * @return string
     */
    public function getSefName()
    {
        return $this->sefName;
    }
}
