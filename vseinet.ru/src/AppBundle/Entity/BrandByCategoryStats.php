<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BrandByCategoryStats.
 *
 * @ORM\Table(name="brand_by_category_stats")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BrandByCategoryStatsRepository")
 */
class BrandByCategoryStats
{
    /**
     * @var int
     *
     * @ORM\Column(name="brand_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $brandId;

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
     * @ORM\Column(name="count_products", type="integer")
     */
    private $countProducts;

    /**
     * @var int
     *
     * @ORM\Column(name="popularity", type="integer")
     */
    private $popularity;

    /**
     * Set brandId.
     *
     * @param int $brandId
     *
     * @return BrandStats
     */
    public function setBrandId($brandId)
    {
        $this->brandId = $brandId;

        return $this;
    }

    /**
     * Get brandId.
     *
     * @return int
     */
    public function getBrandId()
    {
        return $this->brandId;
    }

    /**
     * Set categoryId.
     *
     * @param int $categoryId
     *
     * @return BrandStats
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
     * Set countProducts.
     *
     * @param int $countProducts
     *
     * @return BrandStats
     */
    public function setCountProducts($countProducts)
    {
        $this->countProducts = $countProducts;

        return $this;
    }

    /**
     * Get countProducts.
     *
     * @return int
     */
    public function getCountProducts()
    {
        return $this->countProducts;
    }

    /**
     * Set popularity.
     *
     * @param int $popularity
     *
     * @return BrandStats
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
}
