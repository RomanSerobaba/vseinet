<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ParserSource
 *
 * @ORM\Table(name="parser_source")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\ParserSourceRepository")
 */
class ParserSource
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
     * @ORM\Column(name="supplier_id", type="integer", nullable=true)
     */
    private $supplierId;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string")
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string")
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string")
     */
    private $url;

    /**
     * @var bool
     *
     * @ORM\Column(name="use_anti_guard", type="boolean")
     */
    private $useAntiGuard;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_parse_images", type="boolean")
     */
    private $isParseImages;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer")
     */
    private $sortOrder;

    /**
     * @Assert\Type(type="array<ParserDetailGroups>")
     */
    public $groups;


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
     * Get filename.
     * 
     * @return string
     */
    public function getFilename()
    {
        return $this->getCode().'_'.$this->getAlias().'.js';
    }

    /**
     * Set supplierId
     *
     * @param integer $supplierId
     *
     * @return ParserSource
     */
    public function setSupplierId($supplierId)
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
     * Set code
     *
     * @param string $code
     *
     * @return ParserSource
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
     * Set alias
     *
     * @param string $alias
     *
     * @return ParserSource
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
     * Set url
     *
     * @param string $url
     *
     * @return ParserSource
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set useAntiGuard
     *
     * @param boolean $useAntiGuard
     *
     * @return ParserSource
     */
    public function setUseAntiGuard($useAntiGuard)
    {
        $this->useAntiGuard = $useAntiGuard;

        return $this;
    }

    /**
     * Get useAntiGuard
     *
     * @return bool
     */
    public function getUseAntiGuard()
    {
        return $this->useAntiGuard;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return ParserSource
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
     * Set isParseImages
     *
     * @param boolean $isParseImages
     *
     * @return ParserSource
     */
    public function setIsParseImages($isParseImages)
    {
        $this->isParseImages = $isParseImages;

        return $this;
    }

    /**
     * Get isParseImages
     *
     * @return bool
     */
    public function getIsParseImages()
    {
        return $this->isParseImages;
    }

    /**
     * Set sortOrder
     *
     * @param integer $sortOrder
     *
     * @return ParserSource
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}

