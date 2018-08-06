<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TradeMatrixCategoryCriteria
 *
 * @ORM\Table(name="trade_matrix_category_criteria")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\TradeMatrixCategoryCriteriaRepository")
 */
class TradeMatrixCategoryCriteria
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
     * @var int|null
     *
     * @ORM\Column(name="content_detail_id", type="integer", nullable=true)
     */
    private $contentDetailId;

    /**
     * @var string
     *
     * @ORM\Column(name="category_criteria_type", type="string", length=255)
     */
    private $categoryCriteriaType;


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
     * @return TradeMatrixCategoryCriteria
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
     * Set contentDetailId.
     *
     * @param int|null $contentDetailId
     *
     * @return TradeMatrixCategoryCriteria
     */
    public function setContentDetailId($contentDetailId = null)
    {
        $this->contentDetailId = $contentDetailId;

        return $this;
    }

    /**
     * Get contentDetailId.
     *
     * @return int|null
     */
    public function getContentDetailId()
    {
        return $this->contentDetailId;
    }

    /**
     * Set categoryCriteriaType.
     *
     * @param string $categoryCriteriaType
     *
     * @return TradeMatrixCategoryCriteria
     */
    public function setCategoryCriteriaType($categoryCriteriaType)
    {
        $this->categoryCriteriaType = $categoryCriteriaType;

        return $this;
    }

    /**
     * Get categoryCriteriaType.
     *
     * @return string
     */
    public function getCategoryCriteriaType()
    {
        return $this->categoryCriteriaType;
    }
}
