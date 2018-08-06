<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Inventory
 *
 * @ORM\Table(name="goods_packaging_item")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\GoodsPackagingItemRepository")
 */
class GoodsPackagingItem
{
    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="goods_packaging_did", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $goodsPackagingDId;

    /**
     * @var integer
     *
     * @ORM\Column(name="base_product_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $baseProductId;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity_per_one", type="integer")
     */
    private $quantityPerOne;

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    /**
     * Get goodsPackagingDId
     *
     * @return int
     */
    public function getGoodsPackagingDId()
    {
        return $this->goodsPackagingDId;
    }

    /**
     * Set goodsPackagingDId
     *
     * @param int $goodsPackagingDId
     *
     * @return GoodsPackagingItem
     */
    public function setGoodsPackagingDId($goodsPackagingDId)
    {
        $this->goodsPackagingDId = $goodsPackagingDId;

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
     * Set baseProductId
     *
     * @param int $baseProductId
     *
     * @return GoodsPackagingItem
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get quantityPerOne
     *
     * @return integer
     */
    public function getQuantityPerOne()
    {
        return $this->quantityPerOne;
    }

    /**
     * Set quantityPerOne
     *
     * @param integer $quantityPerOne
     *
     * @return GoodsPackagingItem
     */
    public function setQuantityPerOne($quantityPerOne)
    {
        $this->quantityPerOne = $quantityPerOne;

        return $this;
    }

    // </editor-fold>
}

