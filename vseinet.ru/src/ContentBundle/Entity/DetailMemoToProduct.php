<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetailMemoToProduct
 *
 * @ORM\Table(name="content_detail_memo_to_product")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\DetailMemoToProductRepository")
 */
class DetailMemoToProduct
{
    /**
     * @var int
     *
     * @ORM\Column(name="content_detail_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $detailId;

    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $baseProductId;

    /**
     * @var string
     *
     * @ORM\Column(name="memo", type="string")
     */
    private $memo;


    /**
     * Set detailId
     *
     * @param integer $detailId
     *
     * @return DetailMemoToProduct
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
     * @return DetailMemoToProduct
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
     * Set memo
     *
     * @param string $memo
     *
     * @return DetailMemoToProduct
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;

        return $this;
    }

    /**
     * Get memo
     *
     * @return string
     */
    public function getMemo()
    {
        return $this->memo;
    }
}

