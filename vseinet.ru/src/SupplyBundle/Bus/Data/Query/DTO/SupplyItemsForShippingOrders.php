<?php 

namespace SupplyBundle\Bus\Data\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SupplyItemsForShippingOrders
{
    /**
     * @Assert\Type(type="string")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @Assert\Type(type="integer")
     */
    public $orderId;

    /**
     * @Assert\Type(type="integer")
     */
    public $orderItemId;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasOrder;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isReserved;

    /**
     * @Assert\Type(type="integer")
     */
    public $supplierReserveId;

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
     * @Assert\Type(type="float")
     */
    public $retailPrice;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @Assert\Type(type="string")
     */
    public $client;

    /**
     * @Assert\Type(type="string")
     */
    public $phones;

    /**
     * @Assert\Type(type="string")
     */
    public $city;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasComments;
}