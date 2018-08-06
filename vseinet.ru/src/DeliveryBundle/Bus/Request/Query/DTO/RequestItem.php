<?php 

namespace DeliveryBundle\Bus\Request\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RequestItem
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $deliveryRequestId;

    /**
     * @Assert\Type(type="string")
     */
    public $productName;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;

    public function __construct($id, $deliveryRequestId, $productName, $quantity)
    {
        $this->id = $id;
        $this->deliveryRequestId = $deliveryRequestId;
        $this->productName = $productName;
        $this->quantity = $quantity;
    }
}