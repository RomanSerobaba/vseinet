<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetailType
 *
 * @ORM\Table(name="content_detail_type")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\DetailTypeRepository")
 */
class DetailType
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="abbr", type="string")
     */
    private $abbr;

    /**
     * @var bool
     *
     * @ORM\Column(name="can_be_measured", type="boolean")
     */
    private $canBeMeasured;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_composite", type="boolean")
     */
    private $isComposite;


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
     * Set name
     *
     * @param string $name
     *
     * @return DetailType
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
     * Set abbr
     *
     * @param string $abbr
     *
     * @return DetailType
     */
    public function setAbbr($abbr)
    {
        $this->abbr = $abbr;

        return $this;
    }

    /**
     * Get abbr
     *
     * @return string
     */
    public function getAbbr()
    {
        return $this->abbr;
    }

    /**
     * Set canBeMeasured
     *
     * @param boolean $canBeMeasured
     *
     * @return DetailType
     */
    public function setCanBeMeasured($canBeMeasured)
    {
        $this->canBeMeasured = $canBeMeasured;

        return $this;
    }

    /**
     * Get canBeMeasured
     *
     * @return bool
     */
    public function getCanBeMeasured()
    {
        return $this->canBeMeasured;
    }

    /**
     * Set isComposite
     *
     * @param boolean $isComposite
     *
     * @return DetailType
     */
    public function setIsComposite($isComposite)
    {
        $this->isComposite = $isComposite;

        return $this;
    }

    /**
     * Get isComposite
     *
     * @return bool
     */
    public function getIsComposite()
    {
        return $this->isComposite;
    }
}

