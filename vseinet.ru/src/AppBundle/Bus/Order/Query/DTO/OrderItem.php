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
    public $statusTitle;

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


    public function __construct($quantity, $statusCode, $productName, $baseProductId, $retailPrice)
    {
        $this->quantity = $quantity;
        $this->statusCode = $statusCode;
        $this->statusTitle = OrderItemStatus::getTitle($statusCode);
        $this->tracker = OrderItemStatus::getTracker($statusCode);
        $this->productName = $productName;
        $this->baseProductId = $baseProductId;
        $this->retailPrice = $retailPrice;
        // @todo: delivery date & prepayment
        $this->deliveryDate = new \DateTime();
        $this->prepaymentAmount = 0;
        $this->requiredPrepayment = 0;
    }
}
