<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryStats.
 *
 * @ORM\Table(name="category_stats")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryStatsRepository")
 */
class CategoryStats
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
     * @var int
     *
     * @ORM\Column(name="average_price", type="integer")
     */
    private $averagePrice;

    /**
     * @var int
     *
     * @ORM\Column(name="popularity", type="integer")
     */
    private $popularity;

    /**
     * @var int
     *
     * @ORM\Column(name="sales", type="integer")
     */
    private $sales;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_accessories", type="boolean")
     */
    private $isAccessories;

    /**
     * Set categoryId.
     *
     * @param int $categoryId
     *
     * @return CategoryStats
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
     * Set averagePrice.
     *
     * @param int $averagePrice
     *
     * @return CategoryStats
     */
    public function setAveragePrice($averagePrice)
    {
        $this->averagePrice = $averagePrice;

        return $this;
    }

    /**
     * Get averagePrice.
     *
     * @return int
     */
    public function getAveragePrice()
    {
        return $this->averagePrice;
    }

    /**
     * Set popularity.
     *
     * @param int $popularity
     *
     * @return CategoryStats
     */
    public function setPopularity($popularity)
    {
        $this->popularity = $popularity;

        return $this;
    }

    /**
     * Get popularity.
     *
     * @return int
     */
    public function getPopularity()
    {
        return $this->popularity;
    }

    /**
     * Set sales.
     *
     * @param int $sales
     *
     * @return CategoryStats
     */
    public function setSales($sales)
    {
        $this->sales = $sales;

        return $this;
    }

    /**
     * Get sales.
     *
     * @return int
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * Set isAccessories.
     *
     * @param bool $isAccessories
     *
     * @return CategoryStats
     */
    public function setIsAccessories($isAccessories)
    {
        $this->isAccessories = $isAccessories;

        return $this;
    }

    /**
     * Get isAccessories.
     *
     * @return bool
     */
    public function getIsAccessories()
    {
        return $this->isAccessories;
    }
}
