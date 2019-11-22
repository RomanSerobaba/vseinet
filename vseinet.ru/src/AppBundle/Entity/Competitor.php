<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Competitor
 *
 * @ORM\Table(name="competitor")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompetitorRepository")
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
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="channel", type="string")
     */
    private $channel;

    /**
     * @var string
     *
     * @ORM\Column(name="parse_strategy", type="string")
     */
    private $parseStrategy;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string")
     */
    private $link;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_common_pricing", type="boolean")
     */
    private $isCommonPricing;


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
     * Set channel
     *
     * @param string $channel
     *
     * @return Competitor
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel
     *
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Set parseStrategy
     *
     * @param string $parseStrategy
     *
     * @return Competitor
     */
    public function setParseStrategy($parseStrategy)
    {
        $this->parseStrategy = $parseStrategy;

        return $this;
    }

    /**
     * Get parseStrategy
     *
     * @return string
     */
    public function getParseStrategy()
    {
        return $this->parseStrategy;
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
     * Set isCommonPricing.
     *
     * @param bool $isCommonPricing
     *
     * @return Competitor
     */
    public function setIsCommonPricing($isCommonPricing)
    {
        $this->isCommonPricing = $isCommonPricing;

        return $this;
    }

    /**
     * Get isCommonPricing.
     *
     * @return bool
     */
    public function getIsCommonPricing()
    {
        return $this->isCommonPricing;
    }
}

