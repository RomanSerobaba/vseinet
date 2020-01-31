<?php

namespace AppBundle\Bus\Cart\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation AS VIA;

class Info
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Количество товаров")
     */
    public $total = 0;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Общая сумма товаров")
     */
    public $amount = 0;

    /**
     * @Assert\Type(type="array<AppBundle\Bus\Cart\Query\DTO\ProductInfo>")
     */
    public $products;


    public function __construct(iterable ...$products)
    {
        foreach ($products as $product) {
            $this->total += $product->quantity;
            $this->amount += $product->quantity * $product->price;
            $this->products[$product->id] = $product;
        }
    }
}
