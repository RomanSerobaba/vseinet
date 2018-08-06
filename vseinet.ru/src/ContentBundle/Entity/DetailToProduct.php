<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetailToProduct
 *
 * @ORM\Table(name="content_detail_to_product")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\DetailToProductRepository")
 */
class DetailToProduct
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
     * @ORM\Column(name="content_detail_id", type="integer")
     */
    private $detailId;

    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="content_detail_value_id", type="integer")
     */
    private $valueId;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;
    

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
     * Set detailId
     *
     * @param integer $detailId
     *
     * @return DetailToProduct
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
     * Set baseProductId
     *
     * @param integer $baseProductId
     *
     * @return DetailToProduct
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get baseProductId
     *
     * @return int
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Set valueId
     *
     * @param integer $valueId
     *
     * @return DetailToProduct
     */
    public function setValueId($valueId)
    {
        $this->valueId = $valueId;

        return $this;
    }

    /**
     * Get valueId
     *
     * @return int
     */
    public function getValueId()
    {
        return $this->valueId;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return DetailToProduct
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}

