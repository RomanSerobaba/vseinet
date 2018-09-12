<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TradeMargin
 *
 * @ORM\Table(name="trade_margin")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TradeMarginRepository")
 */
class TradeMargin
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
     * @ORM\Column(name="geo_city_id", type="integer")
     */
    private $geoCityId;

    /**
     * @var int
     *
     * @ORM\Column(name="category_id", type="integer")
     */
    private $categoryId;

    /**
     * @var float
     *
     * @ORM\Column(name="margin_percent", type="float")
     */
    private $marginPercent;

    /**
     * @var float
     *
     * @ORM\Column(name="discount_percent", type="float")
     */
    private $discountPercent;

    /**
     * @var int
     *
     * @ORM\Column(name="lower_limit", type="integer")
     */
    private $lowerLimit;

    /**
     * @var int
     *
     * @ORM\Column(name="higher_limit", type="integer")
     */
    private $higherLimit;


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
     * Set geoCityId
     *
     * @param integer $geoCityId
     *
     * @return TradeMargin
     */
    public function setGeoCityId($geoCityId)
    {
        $this->geoCityId = $geoCityId;

        return $this;
    }

    /**
     * Get geoCityId
     *
     * @return int
     */
    public function getGeoCityId()
    {
        return $this->geoCityId;
    }

    /**
     * Set categoryId
     *
     * @param integer $categoryId
     *
     * @return TradeMargin
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set marginPercent
     *
     * @param float $marginPercent
     *
     * @return TradeMargin
     */
    public function setMarginPercent($marginPercent)
    {
        $this->marginPercent = $marginPercent;

        return $this;
    }

    /**
     * Get marginPercent
     *
     * @return float
     */
    public function getMarginPercent()
    {
        return $this->marginPercent;
    }

    /**
     * Set discountPercent
     *
     * @param float $discountPercent
     *
     * @return TradeMargin
     */
    public function setDiscountPercent($discountPercent)
    {
        $this->discountPercent = $discountPercent;

        return $this;
    }

    /**
     * Get discountPercent
     *
     * @return float
     */
    public function getDiscountPercent()
    {
        return $this->discountPercent;
    }

    /**
     * Set lowerLimit
     *
     * @param integer $lowerLimit
     *
     * @return TradeMargin
     */
    public function setLowerLimit($lowerLimit)
    {
        $this->lowerLimit = $lowerLimit;

        return $this;
    }

    /**
     * Get lowerLimit
     *
     * @return int
     */
    public function getLowerLimit()
    {
        return $this->lowerLimit;
    }

    /**
     * Set higherLimit
     *
     * @param integer $higherLimit
     *
     * @return TradeMargin
     */
    public function setHigherLimit($higherLimit)
    {
        $this->higherLimit = $higherLimit;

        return $this;
    }

    /**
     * Get higherLimit
     *
     * @return int
     */
    public function getHigherLimit()
    {
        return $this->higherLimit;
    }
}

