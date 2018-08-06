<?php 

namespace RegisterBundle\Bus\GoodsReserveRegister\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class Items
{
    /**
     * @Assert\Type(type="array<RegisterBundle\Bus\Template\Query\DTO\Item>")
     */
    public $items;

    /**
     * @Assert\Type(type="integer")
     */
    public $total;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;

    public function __construct($items, $total, $quantity = 0)
    {
        $this->items = array_values($items);
        $this->total = $total ? : 0;
        $this->quantity = $quantity ? : 0;
    }
}