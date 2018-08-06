<?php 

namespace SupplyBundle\Bus\Data\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Uploading
{
    /**
     * @Assert\Type(type="integer")
     */
    public $orderItemId;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $supplierReserveId;

    /**
     * @Assert\Type(type="float")
     */
    public $oldPurchasePrice;

    /**
     * @Assert\Type(type="float")
     */
    public $purchasePrice;

    /**
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

}