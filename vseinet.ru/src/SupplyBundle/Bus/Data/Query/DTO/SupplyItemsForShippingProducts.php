<?php 

namespace SupplyBundle\Bus\Data\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SupplyItemsForShippingProducts
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="float")
     */
    public $purchasePrice;

    /**
     * @Assert\Type(type="float")
     */
    public $bonusPurchasePrice;

    /**
     * @Assert\Type(type="float")
     */
    public $pricelistDiscount;

    /**
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     */
    public $photoUrl;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;
}