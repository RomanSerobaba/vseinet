<?php 

namespace AppBundle\Bus\Order\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class History
{
    /**
     * @Assert\All({
     *     @Assert\Type(type="AppBundle\Bus\Order\Query\DTO\Order")
     * })
     */
    public $orders;

    /**
     * @Assert\Type(type="integer")
     */
    public $total;


    public function __construct(array $history)
    {
        foreach ($history['items'] ?? [] as $order) {
            $this->orders[] = new Order($order);
        }
        $this->total = $history['total'] ?? 0;
    }
}
