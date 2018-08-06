<?php

namespace MatrixBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TradeMatrixProductToRepresentative
 *
 * @ORM\Table(name="trade_matrix_product_to_representative")
 * @ORM\Entity(repositoryClass="MatrixBundle\Repository\TradeMatrixProductToRepresentativeRepository")
 */
class TradeMatrixProductToRepresentative
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
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="representative_id", type="integer")
     */
    private $representativeId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="trade_matrix_template_id", type="integer", nullable=true)
     */
    private $tradeMatrixTemplateId;

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
     * Set baseProductId.
     *
     * @param int $baseProductId
     *
     * @return TradeMatrixProductToRepresentative
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
     * Set representativeId.
     *
     * @param int $representativeId
     *
     * @return TradeMatrixProductToRepresentative
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

    /**
     * Set tradeMatrixTemplateId.
     *
     * @param int|null $tradeMatrixTemplateId
     *
     * @return TradeMatrixProductToRepresentative
     */
    public function setTradeMatrixTemplateId($tradeMatrixTemplateId = null)
    {
        $this->tradeMatrixTemplateId = $tradeMatrixTemplateId;

        return $this;
    }

    /**
     * Get tradeMatrixTemplateId.
     *
     * @return int|null
     */
    public function getTradeMatrixTemplateId()
    {
        return $this->tradeMatrixTemplateId;
    }

    /**
     * Set quantity.
     *
     * @param int $quantity
     *
     * @return TradeMatrixProductToRepresentative
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
}
