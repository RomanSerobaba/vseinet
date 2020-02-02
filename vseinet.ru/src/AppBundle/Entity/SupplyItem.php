<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplyItem.
 *
 * @ORM\Table(name="supply_item")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SupplyItemRepository")
 */
class SupplyItem
{
//    id 	base_product_id 	parent_doc_id 	quantity 	purchase_price 	bonus_amount 	charges
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     */
    public $baseProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="parent_did", type="integer")
     */
    public $parentDid;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    public $quantity;

    /**
     * @var int
     *
     * @ORM\Column(name="purchase_price", type="integer", nullable=true)
     */
    public $purchasePrice;

    /**
     * @var int
     *
     * @ORM\Column(name="bonus_amount", type="integer", options={"default": 0})
     */
    public $bonusAmount;

    /**
     * @var int
     *
     * @ORM\Column(name="extra_discount_amount", type="integer", options={"default": 0})
     */
    public $extraDiscountAmount;

    /**
     * @var int
     *
     * @ORM\Column(name="charges", type="integer", options={"default": 0})
     */
    public $charges;

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
     * @return SupplyItem
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
     * @return SupplyItem
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
     * Set purchasePrice.
     *
     * @param int $purchasePrice
     *
     * @return SupplyItem
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;

        return $this;
    }

    /**
     * Get purchasePrice.
     *
     * @return int
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * Set bonusAmount;.
     *
     * @param int $bonusAmount
     *
     * @return SupplyItem
     */
    public function setBonusAmount($bonusAmount)
    {
        $this->bonusAmount = $bonusAmount;

        return $this;
    }

    /**
     * Get bonusAmount.
     *
     * @return int
     */
    public function getBonusAmount()
    {
        return $this->bonusAmount;
    }

    /**
     * Set extraDiscountAmount;.
     *
     * @param int $extraDiscountAmount
     *
     * @return SupplyItem
     */
    public function setExtraDiscountAmount($extraDiscountAmount)
    {
        $this->extraDiscountAmount = $extraDiscountAmount;

        return $this;
    }

    /**
     * Get extraDiscountAmount.
     *
     * @return int
     */
    public function getExtraDiscountAmount()
    {
        return $this->extraDiscountAmount;
    }

    /**
     * Set charges.
     *
     * @param int $charges
     *
     * @return SupplyItem
     */
    public function setCharges($charges)
    {
        $this->charges = $charges;

        return $this;
    }

    /**
     * Get charges.
     *
     * @return int
     */
    public function getCharges()
    {
        return $this->charges;
    }

    /**
     * Set parentDid.
     *
     * @param int $parentDid
     *
     * @return SupplyItem
     */
    public function setParentDid($parentDid)
    {
        $this->parentDid = $parentDid;

        return $this;
    }

    /**
     * Get parentDid.
     *
     * @return int
     */
    public function getParentDid()
    {
        return $this->parentDid;
    }
}
