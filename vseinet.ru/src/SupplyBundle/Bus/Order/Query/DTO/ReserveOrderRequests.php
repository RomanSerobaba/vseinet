<?php 

namespace SupplyBundle\Bus\Order\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ReserveOrderRequests
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $purchasePrice;

    /**
     * @Assert\Type(type="integer")
     */
    public $price;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isOrderItemComments;

    /**
     * @Assert\Type(type="string")
     */
    public $orderItemStatusCode;

    /**
     * Order constructor.
     */
    public function __construct()
    {
    }
}