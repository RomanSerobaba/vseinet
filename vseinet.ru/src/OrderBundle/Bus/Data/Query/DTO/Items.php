<?php 

namespace OrderBundle\Bus\Data\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class Items
{
    /**
     * @VIA\Description("Заказы")
     * @Assert\Type(type="array<OrderBundle\Bus\Data\Query\DTO\Order>")
     */
    public $items;

    /**
     * @VIA\Description("Общее количество элементов")
     * @Assert\Type(type="integer")
     */
    public $total;

    /**
     * Items constructor.
     * @param $items
     * @param $total
     */
    public function __construct($items, $total = 0)
    {
        $this->items = $items;
        $this->total = $total;
    }
}