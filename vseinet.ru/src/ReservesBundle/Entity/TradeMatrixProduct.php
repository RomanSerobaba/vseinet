<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TradeMatrixProduct
 *
 * @ORM\Table(name="trade_matrix_product")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\TradeMatrixProductRepository")
 */
class TradeMatrixProduct
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
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="updated_by", type="integer", nullable=true)
     */
    private $updatedBy;


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
     * @return TradeMatrixProduct
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
     * @return TradeMatrixProduct
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
     * @param int $quantity
     *
     * @return TradeMatrixProduct
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
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return TradeMatrixProduct
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedBy.
     *
     * @param int|null $updatedBy
     *
     * @return TradeMatrixProduct
     */
    public function setUpdatedBy($updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy.
     *
     * @return int|null
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
}
