<?php

namespace MatrixBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TradeMatrixTemplateProduct
 *
 * @ORM\Table(name="trade_matrix_template_product")
 * @ORM\Entity(repositoryClass="MatrixBundle\Repository\TradeMatrixTemplateProductRepository")
 */
class TradeMatrixTemplateProduct
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
     * @ORM\Column(name="trade_matrix_template_id", type="integer")
     */
    private $tradeMatrixTemplateId;

    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;


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
     * Set tradeMatrixTemplateId.
     *
     * @param int $tradeMatrixTemplateId
     *
     * @return TradeMatrixTemplateProduct
     */
    public function setTradeMatrixTemplateId($tradeMatrixTemplateId)
    {
        $this->tradeMatrixTemplateId = $tradeMatrixTemplateId;

        return $this;
    }

    /**
     * Get tradeMatrixTemplateId.
     *
     * @return int
     */
    public function getTradeMatrixTemplateId()
    {
        return $this->tradeMatrixTemplateId;
    }

    /**
     * Set baseProductId.
     *
     * @param int $baseProductId
     *
     * @return TradeMatrixTemplateProduct
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get baseProductId.
     *
     * @return int
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Set quantity.
     *
     * @param integer $quantity
     *
     * @return TradeMatrixTemplateProduct
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity.
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}
