<?php 

namespace AppBundle\Bus\Order\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;
use AppBundle\Enum\OrderItemStatus;

class OrderItem
{
    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @Enum("AppBundle\Enum\OrderItemStatus")
     */
    public $statusCode;

    /**
     * @Assert\Type(type="string")
     */
    public $statusCodeName;

    /**
     * @Assert\All({
     *     @Enum("AppBundle\Enum\OrderItemStatus")
     * })
     */
    public $tracker;

    /**
     * @Assert\Type(type="string")
     */
    public $productName;

    /**
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @Assert\Type(type="integer")
     */
    public $retailPrice;

    /**
     * @Assert\Type(type="date")
     */
    public $deliveryDate;

    /**
     * @Assert\Type(type="integer")
     */
    public $prepaymentAmount;
    
    /**
     * @Assert\Type(type="integer")
     */
    public $requiredPrepayment;


    public function __construct(array $item)
    {
        $this->quantity = $item['quantity'];
        $this->statusCode = $item['statusCode'];
        $this->statusCodeName = OrderItemStatus::getName($item['statusCode']);
        $this->tracker = OrderItemStatus::getTracker($item['statusCode']);
        $this->productName = $item['productName'];
        $this->baseProductId = $item['baseProductId'];
        $this->retailPrice = $item['retailPrice'];
        $this->deliveryDate = $item['deliveryDate'];
        $this->prepaymentAmount = $item['prepaymentAmount'] ?? 0;
        $this->requiredPrepayment = $item['requiredPrepayment'] ?? 0;
    }
}
