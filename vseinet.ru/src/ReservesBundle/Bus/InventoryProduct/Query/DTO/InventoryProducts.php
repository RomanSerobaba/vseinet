<?php

namespace ReservesBundle\Bus\InventoryProduct\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class InventoryProducts
{
    /**
     * @VIA\Description("Категории списка")
     * @Assert\Type(type="array<ReservesBundle\Bus\InventoryProduct\Query\DTO\InventoryCategory>")
     */
    public $categories;

    /**
     * @VIA\Description("Товары")
     * @Assert\Type(type="array<ReservesBundle\Bus\InventoryProduct\Query\DTO\InventoryProduct>")
     */
    public $products;

    public function __construct($categories, $products)
    {
        $this->categories = $categories;
        $this->products = $products;
    }
}