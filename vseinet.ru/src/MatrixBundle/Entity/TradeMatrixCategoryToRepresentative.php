<?php

namespace MatrixBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TradeMatrixCategoryToRepresentative
 *
 * @ORM\Table(name="trade_matrix_category_to_representative")
 * @ORM\Entity(repositoryClass="MatrixBundle\Repository\TradeMatrixCategoryToRepresentativeRepository")
 */
class TradeMatrixCategoryToRepresentative
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
     * @ORM\Column(name="category_id", type="integer")
     */
    private $categoryId;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var int
     *
     * @ORM\Column(name="representative_id", type="integer")
     */
    private $representativeId;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set categoryId.
     *
     * @param int $categoryId
     *
     * @return TradeMatrixCategoryToRepresentative
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
     * Set quantity.
     *
     * @param int $quantity
     *
     * @return TradeMatrixCategoryToRepresentative
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity.
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set representativeId.
     *
     * @param int $representativeId
     *
     * @return TradeMatrixCategoryToRepresentative
     */
    public function setRepresentativeId($representativeId)
    {
        $this->representativeId = $representativeId;

        return $this;
    }

    /**
     * Get representativeId.
     *
     * @return int
     */
    public function getRepresentativeId()
    {
        return $this->representativeId;
    }
}
