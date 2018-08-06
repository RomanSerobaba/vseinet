<?php

namespace MatrixBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TradeMatrixTemplateToRepresentative
 *
 * @ORM\Table(name="trade_matrix_template_to_representative")
 * @ORM\Entity(repositoryClass="MatrixBundle\Repository\TradeMatrixTemplateToRepresentativeRepository")
 */
class TradeMatrixTemplateToRepresentative
{

    /**
     * @var int
     *
     * @ORM\Column(name="trade_matrix_template_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $tradeMatrixTemplateId;

    /**
     * @var int
     *
     * @ORM\Column(name="representative_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $representativeId;


    /**
     * Set tradeMatrixTemplateId.
     *
     * @param int $tradeMatrixTemplateId
     *
     * @return TradeMatrixTemplateToRepresentative
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
     * Set representativeId.
     *
     * @param int $representativeId
     *
     * @return TradeMatrixTemplateToRepresentative
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
