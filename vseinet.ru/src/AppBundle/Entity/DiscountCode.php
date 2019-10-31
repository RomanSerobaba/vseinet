<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DiscountCode
 *
 * @ORM\Table(name="discount_code")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DiscountCodeRepository")
 */
class DiscountCode
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
     * @ORM\Column(name="code", type="string")
     */
    private $code;

    /**
     * @var float
     *
     * @ORM\Column(name="percent", type="float")
     */
    private $percent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="since_date", type="datetime")
     */
    private $sinceDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="till_date", type="datetime")
     */
    private $tillDate;


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
     * Set code
     *
     * @param string $code
     *
     * @return DiscountCode
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
     * Set percent
     *
     * @param string $percent
     *
     * @return DiscountCode
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * Get percent
     *
     * @return string
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * Set sinceDate
     *
     * @param \DateTime $sinceDate
     *
     * @return DiscountCode
     */
    public function setSinceDate($sinceDate)
    {
        $this->sinceDate = $sinceDate;

        return $this;
    }

    /**
     * Get sinceDate
     *
     * @return \DateTime
     */
    public function getSinceDate()
    {
        return $this->sinceDate;
    }

    /**
     * Set tillDate
     *
     * @param \DateTime $tillDate
     *
     * @return DiscountCode
     */
    public function setTillDate($tillDate)
    {
        $this->tillDate = $tillDate;

        return $this;
    }

    /**
     * Get tillDate
     *
     * @return \DateTime
     */
    public function getTillDate()
    {
        return $this->tillDate;
    }
}

