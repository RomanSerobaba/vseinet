<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParserDetailToProduct
 *
 * @ORM\Table(name="parser_detail_to_product")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\ParserDetailToProductRepository")
 */
class ParserDetailToProduct
{
    /**
     * @var int
     *
     * @ORM\Column(name="parser_product_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $productId;

    /**
     * @var int
     *
     * @ORM\Column(name="parser_detail_group_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $groupId;

    /**
     * @var int
     *
     * @ORM\Column(name="parser_detail_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $detailId;

    /**
     * @var int
     *
     * @ORM\Column(name="parser_detail_value_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $valueId;


    /**
     * Set productId
     *
     * @param integer $productId
     *
     * @return ParserDetailToProduct
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set groupId
     *
     * @param integer $groupId
     *
     * @return ParserDetailToProduct
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set detailId
     *
     * @param integer $detailId
     *
     * @return ParserDetailToProduct
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
     * Set valueId
     *
     * @param integer $valueId
     *
     * @return ParserDetailToProduct
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
}

