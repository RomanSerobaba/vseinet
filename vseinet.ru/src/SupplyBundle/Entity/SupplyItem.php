<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplyItem
 *
 * @ORM\Table(name="supply_item")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\SupplyItemRepository")
 */
class SupplyItem
{
//    id 	base_product_id 	parent_doc_type 	parent_doc_id 	quantity 	purchase_price 	bonus_amount 	charges
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
     * @var string
     *
     * @ORM\Column(name="parent_doc_type", type="string")
     */
    private $parentDocType;
    
    /**
     * @var int
     *
     * @ORM\Column(name="parent_doc_id", type="integer")
     */
    private $parentDocId;
    
    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @var int
     *
     * @ORM\Column(name="purchase_price", type="integer", nullable=true)
     */
    private $purchasePrice;
    
    /**
     * @var int
     *
     * @ORM\Column(name="bonus_amount", type="integer", options={"default": 0})
     */
    private $bonusAmount;

    /**
     * @var int
     *
     * @ORM\Column(name="charges", type="integer", options={"default": 0})
     */
    private $charges;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set baseProductId
     *
     * @param integer $baseProductId
     *
     * @return SupplyItem
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return SupplyItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
    
    /**
     * Set purchasePrice
     *
     * @param integer $purchasePrice
     *
     * @return SupplyItem
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;

        return $this;
    }

    /**
     * Get purchasePrice
     *
     * @return int
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * Set bonusAmount;
     *
     * @param integer $bonusAmount
     *
     * @return SupplyItem
     */
    public function setBonusAmount($bonusAmount)
    {
        $this->bonusAmount = $bonusAmount;

        return $this;
    }

    /**
     * Get bonusAmount
     *
     * @return int
     */
    public function getBonusAmount()
    {
        return $this->bonusAmount;
    }
    

    /**
     * Set charges
     *
     * @param integer $charges
     *
     * @return SupplyItem
     */
    public function setCharges($charges)
    {
        $this->charges = $charges;

        return $this;
    }

    /**
     * Get charges
     *
     * @return int
     */
    public function getCharges()
    {
        return $this->charges;
    }
    
    /**
     * Set parentDocId
     *
     * @param integer $parentDocId
     *
     * @return SupplyItem
     */
    public function setParentDocId($parentDocId)
    {
        $this->parentDocId = $parentDocId;

        return $this;
    }

    /**
     * Get parentDocId
     *
     * @return int
     */
    public function getParentDocId()
    {
        return $this->parentDocId;
    }

    /**
     * Set parentDocType
     *
     * @param string $parentDocType
     *
     * @return SupplyItem
     */
    public function setParentDocType($parentDocType)
    {
        $this->parentDocType = $parentDocType;

        return $this;
    }

    /**
     * Get parentDocType
     *
     * @return string
     */
    public function getParentDocType()
    {
        return $this->parentDocType;
    }
    
    public function __construct()
    {
        $this->bonusAmount = 0;
        $this->charges = 0;
    }

}

