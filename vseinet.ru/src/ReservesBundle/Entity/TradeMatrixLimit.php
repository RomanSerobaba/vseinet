<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TradeMatrixLimit
 *
 * @ORM\Table(name="trade_matrix_limit")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\TradeMatrixLimitRepository")
 */
class TradeMatrixLimit
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
     * @ORM\Column(name="representative_id", type="integer")
     */
    private $representativeId;

    /**
     * @var int
     *
     * @ORM\Column(name="limit_amount", type="integer")
     */
    private $limitAmount;


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
     * Set representativeId.
     *
     * @param int $representativeId
     *
     * @return TradeMatrixLimit
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
     * Set limitAmount.
     *
     * @param int $limitAmount
     *
     * @return TradeMatrixLimit
     */
    public function setLimitAmount($limitAmount)
    {
        $this->limitAmount = $limitAmount;

        return $this;
    }

    /**
     * Get limitAmount.
     *
     * @return int
     */
    public function getLimitAmount()
    {
        return $this->limitAmount;
    }
}
