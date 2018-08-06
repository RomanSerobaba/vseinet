<?php 

namespace SiteBundle\Bus\Cart\Query\DTO;

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
     */
    public $amountDiscount;

    /**
     * @Assert\Type(type="string")
     */
    public $discountCode;

    /**
     * @Assert\Type(type="float")
     */
    public $discount;

    /**
     * @Assert\Type(type="array<SiteBundle\Bus\Cart\Query\DTO\Product>")
     */
    public $products;


    public function __construct(Product ...$products)
    {
        foreach ($products as $product) {
            $this->total += $product->quantity;
            $this->amount += $product->quantity * $product->price;
            if ($this->discount) {
                $product->priceDiscount = round($product->price * (1 - $this->discount / 100), -2);
            }
            $this->amountDiscount += $product->quantity * $product->priceDiscount;
            $this->products[$product->id] = $product;
        }
    }
}
