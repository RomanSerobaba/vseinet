<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ColorCompositeSchema
 *
 * @ORM\Table(name="color_composite_schema")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\ColorCompositeSchemaRepository")
 */
class ColorCompositeSchema
{
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name_male", type="string", nullable=true)
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
     * @ORM\Column(name="name_plural", type="string", nullable=true)
     */
    private $namePlural;

    /**
     * @var bool
     *
     * @ORM\Column(name="use_as_main", type="boolean")
     */
    private $useAsMain;

    /**
     * @var bool
     *
     * @ORM\Column(name="use_as_addon", type="boolean")
     */
    private $useAsAddon;

    /**
     * @var bool
     *
     * @ORM\Column(name="use_with_addons", type="boolean")
     */
    private $useWithAddons;


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
     * Get nameMale
     *
     * @return string
     */
    public function getNameMale()
    {
        return $this->nameMale;
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
     * Get nameNeuter
     *
     * @return string
     */
    public function getNameNeuter()
    {
        return $this->nameNeuter;
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
     * Get useAsMain
     *
     * @return bool
     */
    public function getUseAsMain()
    {
        return $this->useAsMain;
    }

    /**
     * Get useAsAddon
     *
     * @return bool
     */
    public function getUseAsAddon()
    {
        return $this->useAsAddon;
    }

    /**
     * Get useWithAddons
     *
     * @return bool
     */
    public function getUseWithAddons()
    {
        return $this->useWithAddons;
    }

    /**
     * Get name
     *
     * @param string $gender
     *  
     * @return string
     */
    public function getName($gender)
    {
        $method = 'getName'.ucfirst($gender);
        if (!method_exists($this, $method)) {
            throw new \LogicException(sprintf('Неверное название цветовой схемы "%s"', $gender));
        }

        return $this->{$method}() ?: $this->getNameMale();
    }
}

