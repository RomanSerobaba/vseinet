<?php 

namespace AppBundle\Bus\Order\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Order
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="datetime")
     */
    public $createdAt;

    /**
     * @Assert\All({
     *     @Assert\Type(type="AppBundle\Bus\Order\Query\DTO\OrderItem")
     * })
     */
    public $items;

    /**
     * @Assert\Type(type="integer")
     */
    public $amount;


    public function __construct(int $id, \DateTime $createdAt, array $items)
    {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->amount = 0;

        foreach ($items as $item) {
            $this->items[] = new OrderItem(
                $item['quantity'], 
                $item['statusCode'], 
                $item['productName'], 
                $item['baseProductId'], 
                $item['retailPrice']
            );
            $this->amount += $item['quantity'] * $item['retailPrice'];
        }
    }
}
