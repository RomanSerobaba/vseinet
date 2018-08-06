<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientOrderItem
 *
 * @ORM\Table(name="client_order_item")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\ClientOrderItemRepository")
 */
class ClientOrderItem
{
    /**
     * @var int
     *
     * @ORM\Column(name="order_item_id", type="integer")
     * @ORM\Id
     */
    private $orderItemId;

    /**
     * @var int
     *
     * @ORM\Column(name="retail_price", type="integer")
     */
    private $retailPrice;

    /**
     * @var int|null
     *
     * @ORM\Column(name="franchiser_client_price", type="integer", nullable=true)
     */
    private $franchiserClientPrice;

    /**
     * @var int
     *
     * @ORM\Column(name="required_prepayment", type="integer")
     */
    private $requiredPrepayment;

    /**
     * @var int
     *
     * @ORM\Column(name="reserved_prepayment", type="integer")
     */
    private $reservedPrepayment;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_clarification_needed", type="boolean")
     */
    private $isClarificationNeeded;

    /**
     * @var int
     *
     * @ORM\Column(name="initial_retail_price", type="integer")
     */
    private $initialRetailPrice;

    /**
     * @var int
     *
     * @ORM\Column(name="discount_amount", type="integer")
     */
    private $discountAmount;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="retail_price_updated_at", type="datetime", nullable=true)
     */
    private $retailPriceUpdatedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="retail_price_updated_by", type="integer", nullable=true)
     */
    private $retailPriceUpdatedBy;


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
     * Set orderItemId.
     *
     * @param int $orderItemId
     *
     * @return ClientOrderItem
     */
    public function setOrderItemId($orderItemId)
    {
        $this->orderItemId = $orderItemId;

        return $this;
    }

    /**
     * Get orderItemId.
     *
     * @return int
     */
    public function getOrderItemId()
    {
        return $this->orderItemId;
    }

    /**
     * Set retailPrice.
     *
     * @param int $retailPrice
     *
     * @return ClientOrderItem
     */
    public function setRetailPrice($retailPrice)
    {
        $this->retailPrice = $retailPrice;

        return $this;
    }

    /**
     * Get retailPrice.
     *
     * @return int
     */
    public function getRetailPrice()
    {
        return $this->retailPrice;
    }

    /**
     * Set franchiserClientPrice.
     *
     * @param int|null $franchiserClientPrice
     *
     * @return ClientOrderItem
     */
    public function setFranchiserClientPrice($franchiserClientPrice = null)
    {
        $this->franchiserClientPrice = $franchiserClientPrice;

        return $this;
    }

    /**
     * Get franchiserClientPrice.
     *
     * @return int|null
     */
    public function getFranchiserClientPrice()
    {
        return $this->franchiserClientPrice;
    }

    /**
     * Set requiredPrepayment.
     *
     * @param int $requiredPrepayment
     *
     * @return ClientOrderItem
     */
    public function setRequiredPrepayment($requiredPrepayment)
    {
        $this->requiredPrepayment = $requiredPrepayment;

        return $this;
    }

    /**
     * Get requiredPrepayment.
     *
     * @return int
     */
    public function getRequiredPrepayment()
    {
        return $this->requiredPrepayment;
    }

    /**
     * Set reservedPrepayment.
     *
     * @param int $reservedPrepayment
     *
     * @return ClientOrderItem
     */
    public function setReservedPrepayment($reservedPrepayment)
    {
        $this->reservedPrepayment = $reservedPrepayment;

        return $this;
    }

    /**
     * Get reservedPrepayment.
     *
     * @return int
     */
    public function getReservedPrepayment()
    {
        return $this->reservedPrepayment;
    }

    /**
     * Set isClarificationNeeded.
     *
     * @param bool $isClarificationNeeded
     *
     * @return ClientOrderItem
     */
    public function setIsClarificationNeeded($isClarificationNeeded)
    {
        $this->isClarificationNeeded = $isClarificationNeeded;

        return $this;
    }

    /**
     * Get isClarificationNeeded.
     *
     * @return bool
     */
    public function getIsClarificationNeeded()
    {
        return $this->isClarificationNeeded;
    }

    /**
     * Set initialRetailPrice.
     *
     * @param int $initialRetailPrice
     *
     * @return ClientOrderItem
     */
    public function setInitialRetailPrice($initialRetailPrice)
    {
        $this->initialRetailPrice = $initialRetailPrice;

        return $this;
    }

    /**
     * Get initialRetailPrice.
     *
     * @return int
     */
    public function getInitialRetailPrice()
    {
        return $this->initialRetailPrice;
    }

    /**
     * Set discountAmount.
     *
     * @param int $discountAmount
     *
     * @return ClientOrderItem
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    /**
     * Get discountAmount.
     *
     * @return int
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * Set retailPriceUpdatedAt.
     *
     * @param \DateTime|null $retailPriceUpdatedAt
     *
     * @return ClientOrderItem
     */
    public function setRetailPriceUpdatedAt($retailPriceUpdatedAt = null)
    {
        $this->retailPriceUpdatedAt = $retailPriceUpdatedAt;

        return $this;
    }

    /**
     * Get retailPriceUpdatedAt.
     *
     * @return \DateTime|null
     */
    public function getRetailPriceUpdatedAt()
    {
        return $this->retailPriceUpdatedAt;
    }

    /**
     * Set retailPriceUpdatedBy.
     *
     * @param int|null $retailPriceUpdatedBy
     *
     * @return ClientOrderItem
     */
    public function setRetailPriceUpdatedBy($retailPriceUpdatedBy = null)
    {
        $this->retailPriceUpdatedBy = $retailPriceUpdatedBy;

        return $this;
    }

    /**
     * Get retailPriceUpdatedBy.
     *
     * @return int|null
     */
    public function getRetailPriceUpdatedBy()
    {
        return $this->retailPriceUpdatedBy;
    }
}
