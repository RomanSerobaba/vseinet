<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BaseProductNaming
 *
 * @ORM\Table(name="base_product_naming")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\BaseProductNamingRepository")
 */
class BaseProductNaming
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
     * @ORM\Column(name="content_detail_id", type="integer")
     */
    private $detailId;

    /**
     * @var string
     *
     * @ORM\Column(name="field_name", type="string", nullable=true)
     */
    private $fieldName;

    /**
     * @var string
     *
     * @ORM\Column(name="delimiter_before", type="string", nullable=true)
     */
    private $delimiterBefore;

    /**
     * @var string
     *
     * @ORM\Column(name="delimiter_after", type="string", nullable=true)
     */
    private $delimiterAfter;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_required", type="boolean")
     */
    private $isRequired;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer")
     */
    private $sortOrder;


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
     * Set categoryId
     *
     * @param integer $categoryId
     *
     * @return BaseProductNaming
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
     * Set detailId
     *
     * @param integer $detailId
     *
     * @return BaseProductNaming
     */
    public function setDetailId($detailId)
    {
        $this->detailId = $detailId;

        return $this;
    }

    /**
     * Get detailId
     *
     * @return int
     */
    public function getDetailId()
    {
        return $this->detailId;
    }

    /**
     * Set fieldName
     *
     * @param string $fieldName
     *
     * @return BaseProductNaming
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    /**
     * Get fieldName
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * Set delimiterBefore
     *
     * @param string $delimiterBefore
     *
     * @return BaseProductNaming
     */
    public function setDelimiterBefore($delimiterBefore)
    {
        $this->delimiterBefore = $delimiterBefore;

        return $this;
    }

    /**
     * Get delimiterBefore
     *
     * @return string
     */
    public function getDelimiterBefore($clear = false)
    {
        if ($clear) {
            if ('_' == $this->delimiterBefore) {
                return ' ';
            }

            return $this->delimiterBefore ?: '';
        }

        return $this->delimiterBefore;
    }

    /**
     * Set delimiterAfter
     *
     * @param string $delimiterAfter
     *
     * @return BaseProductNaming
     */
    public function setDelimiterAfter($delimiterAfter)
    {
        $this->delimiterAfter = $delimiterAfter;

        return $this;
    }

    /**
     * Get delimiterAfter
     *
     * @return string
     */
    public function getDelimiterAfter($clear = false)
    {
        if ($clear) {
            if ('_' == $this->delimiterAfter) {
                return ' ';
            }

            return $this->delimiterAfter ?: '';
        }
        
        return $this->delimiterAfter;
    }

    /**
     * Set isRequired
     *
     * @param boolean $isRequired
     *
     * @return BaseProductNaming
     */
    public function setIsRequired($isRequired)
    {
        $this->isRequired = $isRequired;

        return $this;
    }

    /**
     * Get isRequired
     *
     * @return bool
     */
    public function getIsRequired()
    {
        return $this->isRequired;
    }

    /**
     * Set sortOrder
     *
     * @param integer $sortOrder
     *
     * @return BaseProductNaming
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}

