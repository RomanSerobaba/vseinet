<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Color
 *
 * @ORM\Table(name="color")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\ColorRepository")
 */
class Color
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
     * @ORM\Column(name="color_palette_id", type="integer")
     */
    private $paletteId;

    /**
     * @var int
     *
     * @ORM\Column(name="value_dec", type="integer")
     */
    private $valueDec;

    /**
     * @var string
     *
     * @ORM\Column(name="value_hex", type="string")
     */
    private $valueHex;

    /**
     * @var string
     *
     * @ORM\Column(name="name_male", type="string")
     */
    private $nameMale;

    /**
     * @var string
     *
     * @ORM\Column(name="name_female", type="string", nullable=true)
     */
    private $nameFemale;

    /**
     * @var string
     *
     * @ORM\Column(name="name_neuter", type="string", nullable=true)
     */
    private $nameNeuter;

    /**
     * @var string
     *
     * @ORM\Column(name="name_ablative", type="string", nullable=true)
     */
    private $nameAblative;

    /**
     * @var string
     *
     * @ORM\Column(name="name_plural", type="string", nullable=true)
     */
    private $namePlural;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer")
     */
    private $sortOrder;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_base", type="boolean")
     */
    private $isBase;


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
     * Get name by gender
     * 
     * @param string $gender
     * 
     * @return string
     */
    public function getNameByGender($gender)
    {
        return $this->{'getName'.ucfirst($gender)}() ?: $this->geNameMale();
    }

    /**
     * Set paletteId
     *
     * @param integer $paletteId
     *
     * @return Color
     */
    public function setPaletteId($paletteId)
    {
        $this->paletteId = $paletteId;

        return $this;
    }

    /**
     * Get paletteId
     *
     * @return int
     */
    public function getPaletteId()
    {
        return $this->paletteId;
    }

    /**
     * Set valueDec
     *
     * @param integer $valueDec
     *
     * @return Color
     */
    public function setValueDec($valueDec)
    {
        $this->valueDec = $valueDec;

        return $this;
    }

    /**
     * Get valueDec
     *
     * @return int
     */
    public function getValueDec()
    {
        return $this->valueDec;
    }

    /**
     * Set valueHex
     *
     * @param string $valueHex
     *
     * @return Color
     */
    public function setValueHex($valueHex)
    {
        $this->valueHex = $valueHex;

        return $this;
    }

    /**
     * Get valueHex
     *
     * @return string
     */
    public function getValueHex()
    {
        return $this->valueHex;
    }

    /**
     * Set nameMale
     *
     * @param string $nameMale
     *
     * @return Color
     */
    public function setNameMale($nameMale)
    {
        $this->nameMale = $nameMale;

        return $this;
    }

    /**
     * Get nameMale
     *
     * @return string
     */
    public function getNameMale()
    {
        return $this->nameMale;
    }

    /**
     * Set nameFemale
     *
     * @param string $nameFemale
     *
     * @return Color
     */
    public function setNameFemale($nameFemale)
    {
        $this->nameFemale = $nameFemale;

        return $this;
    }

    /**
     * Get nameFemale
     *
     * @return string
     */
    public function getNameFemale()
    {
        return $this->nameFemale;
    }

    /**
     * Set nameNeuter
     *
     * @param string $nameNeuter
     *
     * @return Color
     */
    public function setNameNeuter($nameNeuter)
    {
        $this->nameNeuter = $nameNeuter;

        return $this;
    }

    /**
     * Get nameNeuter
     *
     * @return string
     */
    public function getNameNeuter()
    {
        return $this->nameNeuter;
    }

    /**
     * Set nameAblative
     *
     * @param string $nameAblative
     *
     * @return Color
     */
    public function setNameAblative($nameAblative)
    {
        $this->nameAblative = $nameAblative;

        return $this;
    }

    /**
     * Get nameAblative
     *
     * @return string
     */
    public function getNameAblative()
    {
        return $this->nameAblative;
    }

    /**
     * Set namePlural
     *
     * @param string $namePlural
     *
     * @return Color
     */
    public function setNamePlural($namePlural)
    {
        $this->namePlural = $namePlural;

        return $this;
    }

    /**
     * Get namePlural
     *
     * @return string
     */
    public function getNamePlural()
    {
        return $this->namePlural;
    }

    /**
     * Set sortOrder
     *
     * @param integer $sortOrder
     *
     * @return Color
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

    /**
     * Set isBase
     *
     * @param boolean $isBase
     *
     * @return Color
     */
    public function setIsBase($isBase)
    {
        $this->isBase = $isBase;

        return $this;
    }

    /**
     * Get isBase
     *
     * @return bool
     */
    public function getIsBase()
    {
        return $this->isBase;
    }
}

