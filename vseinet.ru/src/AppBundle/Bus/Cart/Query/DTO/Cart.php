<?php 

namespace AppBundle\Bus\Cart\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation AS VIA;

class Cart
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
     * @Assert\Type(type="integer")
     * @VIA\Description("Общая сумма товаров со скидкой")
     */
    public $amountDiscount = 0;

    /**
     * @Assert\Type(type="string")
     */
    public $discountCode;

    /**
     * @Assert\Type(type="array<AppBundle\Bus\Cart\Query\DTO\Product>")
     */
    public $products;


    public function __construct(array $products, ?string $discountCode)
    {
        foreach ($products as $product) {
            $this->total += $product->quantity;
            $this->amount += $product->quantity * $product->price;
            $this->amountDiscount += $product->quantity * $product->priceDiscount;
            $this->products[$product->id] = $product;
        }
        $this->discountCode = $discountCode;
    }
}
