<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MeasureUnit
 *
 * @ORM\Table(name="content_measure_unit")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MeasureUnitRepository")
 */
class MeasureUnit
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
     * @ORM\Column(name="content_measure_id", type="integer")
     */
    private $measureId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="k", type="float")
     */
    private $k;

    /**
     * @var bool
     *
     * @ORM\Column(name="use_space", type="boolean")
     */
    private $useSpace;

    /**
     * @var MeasureUnitAlias[]
     * 
     * @Assert\Type(type="array<MeasureUnitAlias>")
     */
    public $aliases;


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
     * Set measureId
     *
     * @param integer $measureId
     *
     * @return MeasureUnit
     */
    public function setMeasureId($measureId)
    {
        $this->measureId = $measureId;

        return $this;
    }

    /**
     * Get measureId
     *
     * @return int
     */
    public function getMeasureId()
    {
        return $this->measureId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return MeasureUnit
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
     * Set k
     *
     * @param float $k
     *
     * @return MeasureUnit
     */
    public function setK($k)
    {
        $this->k = $k;

        return $this;
    }

    /**
     * Get k
     *
     * @return float
     */
    public function getK()
    {
        return $this->k;
    }

    /**
     * Set useSpace
     *
     * @param boolean $useSpace
     *
     * @return MeasureUnit
     */
    public function setUseSpace($useSpace)
    {
        $this->useSpace = $useSpace;

        return $this;
    }

    /**
     * Get useSpace
     *
     * @return bool
     */
    public function getUseSpace()
    {
        return $this->useSpace;
    }
}

