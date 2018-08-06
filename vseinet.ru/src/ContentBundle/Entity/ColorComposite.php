<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * ColorComposite
 *
 * @ORM\Table(name="color_composite")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\ColorCompositeRepository")
 */
class ColorComposite
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
     * @ORM\Column(name="schema_type", type="string", nullable=true)
     */
    private $schemaType;

    /**
     * @var int
     *
     * @ORM\Column(name="color_id_1", type="integer", nullable=true)
     * @JMS\Exclude
     */
    private $colorId1;

    /**
     * @var int
     *
     * @ORM\Column(name="color_id_2", type="integer", nullable=true)
     * @JMS\Exclude
     */
    private $colorId2;

    /**
     * @var int
     *
     * @ORM\Column(name="color_id_3", type="integer", nullable=true)
     * @JMS\Exclude
     */
    private $colorId3;

    /**
     * @var int
     *
     * @ORM\Column(name="color_id_4", type="integer", nullable=true)
     * @JMS\Exclude
     */
    private $colorId4;

    /**
     * @var bool
     *
     * @ORM\Column(name="with_picture", type="boolean")
     */
    private $withPicture;

    /**
     * @var string
     *
     * @ORM\Column(name="picture_name", type="string", nullable=true)
     */
    private $pictureName;

    /**
     * @var string
     *
     * @ORM\Column(name="formed_value", type="string")
     */
    private $formedValue;

    /**
     * @JMS\Type("array<ContentBundle\Entity\Color>")
     */
    public $colors;


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
     * Set schemaType
     *
     * @param string $schemaType
     *
     * @return ColorComposite
     */
    public function setSchemaType($schemaType)
    {
        $this->schemaType = $schemaType;

        return $this;
    }

    /**
     * Get schemaType
     *
     * @return string
     */
    public function getSchemaType()
    {
        return $this->schemaType;
    }

    /**
     * Set colorId1
     *
     * @param string $colorId1
     *
     * @return ColorComposite
     */
    public function setColorId1($colorId1)
    {
        $this->colorId1 = $colorId1;

        return $this;
    }

    /**
     * Get colorId1
     *
     * @return string
     */
    public function getColorId1()
    {
        return $this->colorId1;
    }

    /**
     * Set colorId2
     *
     * @param integer $colorId2
     *
     * @return ColorComposite
     */
    public function setColorId2($colorId2)
    {
        $this->colorId2 = $colorId2;

        return $this;
    }

    /**
     * Get colorId2
     *
     * @return int
     */
    public function getColorId2()
    {
        return $this->colorId2;
    }

    /**
     * Set colorId3
     *
     * @param integer $colorId3
     *
     * @return ColorComposite
     */
    public function setColorId3($colorId3)
    {
        $this->colorId3 = $colorId3;

        return $this;
    }

    /**
     * Get colorId3
     *
     * @return int
     */
    public function getColorId3()
    {
        return $this->colorId3;
    }

    /**
     * Set colorId4
     *
     * @param integer $colorId4
     *
     * @return ColorComposite
     */
    public function setColorId4($colorId4)
    {
        $this->colorId4 = $colorId4;

        return $this;
    }

    /**
     * Get colorId4
     *
     * @return int
     */
    public function getColorId4()
    {
        return $this->colorId4;
    }

    /**
     * Set withPicture
     *
     * @param boolean $withPicture
     *
     * @return ColorComposite
     */
    public function setWithPicture($withPicture)
    {
        $this->withPicture = $withPicture;

        return $this;
    }

    /**
     * Get withPicture
     *
     * @return bool
     */
    public function getWithPicture()
    {
        return $this->withPicture;
    }

    /**
     * Set pictureName
     *
     * @param string $pictureName
     *
     * @return ColorComposite
     */
    public function setPictureName($pictureName)
    {
        $this->pictureName = $pictureName;

        return $this;
    }

    /**
     * Get pictureName
     *
     * @return string
     */
    public function getPictureName()
    {
        return $this->pictureName;
    }

    /**
     * Set formedValue
     *
     * @param string $formedValue
     *
     * @return ColorComposite
     */
    public function setFormedValue($formedValue)
    {
        $this->formedValue = $formedValue;

        return $this;
    }

    /**
     * Get formedValue
     *
     * @return string
     */
    public function getFormedValue()
    {
        return $this->formedValue;
    }
}

