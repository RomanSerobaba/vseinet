<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fullness
 *
 * @ORM\Table(name="content_fullness")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\FullnessRepository")
 */
class Fullness
{
    /**
     * @var int
     *
     * @ORM\Column(name="category_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $categoryId;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $subject;

    /**
     * @var int
     *
     * @ORM\Column(name="total", type="integer")
     */
    private $total;

    /**
     * @var int
     *
     * @ORM\Column(name="active", type="integer")
     */
    private $active;

    /**
     * @var int
     *
     * @ORM\Column(name="count", type="integer")
     */
    private $count;

    /**
     * @var int
     *
     * @ORM\Column(name="count_from_parser", type="integer")
     */
    private $countFromParser;

    /**
     * @var float
     *
     * @ORM\Column(name="percent_fullness", type="float")
     */
    private $percentFullness;

    /**
     * @var int
     *
     * @ORM\Column(name="count_active", type="integer")
     */
    private $countActive;

    /**
     * @var int
     *
     * @ORM\Column(name="count_active_from_parser", type="integer")
     */
    private $countActiveFromParser;

    /**
     * @var float
     *
     * @ORM\Column(name="active_percent_fullness", type="float")
     */
    private $activePercentFullness;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;


    /**
     * Set categoryId.
     *
     * @param int $categoryId
     *
     * @return Fullness
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId.
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set subject.
     *
     * @param string $subject
     *
     * @return Fullness
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set total.
     *
     * @param int $total
     *
     * @return Fullness
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set active.
     *
     * @param int $active
     *
     * @return Fullness
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active.
     *
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set count.
     *
     * @param int $count
     *
     * @return Fullness
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count.
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set countFromParser.
     *
     * @param int $countFromParser
     *
     * @return Fullness
     */
    public function setCountFromParser($countFromParser)
    {
        $this->countFromParser = $countFromParser;

        return $this;
    }

    /**
     * Get countFromParser.
     *
     * @return int
     */
    public function getCountFromParser()
    {
        return $this->countFromParser;
    }

    /**
     * Set percentFullness.
     *
     * @param float $percentFullness
     *
     * @return Fullness
     */
    public function setPercentFullness($percentFullness)
    {
        $this->percentFullness = $percentFullness;

        return $this;
    }

    /**
     * Get percentFullness.
     *
     * @return float
     */
    public function getPercentFullness()
    {
        return $this->percentFullness;
    }

    /**
     * Set countActive.
     *
     * @param int $countActive
     *
     * @return Fullness
     */
    public function setCountActive($countActive)
    {
        $this->countActive = $countActive;

        return $this;
    }

    /**
     * Get countActive.
     *
     * @return int
     */
    public function getCountActive()
    {
        return $this->countActive;
    }

    /**
     * Set countActiveFromParser.
     *
     * @param int $countActiveFromParser
     *
     * @return Fullness
     */
    public function setCountActiveFromParser($countActiveFromParser)
    {
        $this->countActiveFromParser = $countActiveFromParser;

        return $this;
    }

    /**
     * Get countActiveFromParser.
     *
     * @return int
     */
    public function getCountActiveFromParser()
    {
        return $this->countActiveFromParser;
    }

    /**
     * Set activePercentFullness.
     *
     * @param float $activePercentFullness
     *
     * @return Fullness
     */
    public function setActivePercentFullness($activePercentFullness)
    {
        $this->activePercentFullness = $activePercentFullness;

        return $this;
    }

    /**
     * Get activePercentFullness.
     *
     * @return float
     */
    public function getActivePercentFullness()
    {
        return $this->activePercentFullness;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return Fullness
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
