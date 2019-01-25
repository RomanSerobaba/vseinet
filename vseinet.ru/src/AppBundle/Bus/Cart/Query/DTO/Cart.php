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
     * @Assert\Type(type="bool")
     * @VIA\Description("Признак того, есть ли среди товаров строительные материалы")
     */
    public $hasStroika = false;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Общая сумма товаров со скидкой")
     */
    public $amountWithDiscount = 0;

    /**
     * @Assert\Type(type="string")
     */
    public $discountCode;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @Assert\All(
     *  @Assert\Type(type="AppBundle\Bus\Cart\Query\DTO\Product")
     * )
     */
    public $products;


    public function __construct(array $products, string $discountCode = NULL, int $geoPointId = NULL)
    {
        foreach ($products as $key => $product) {
            $this->total += $product->quantity;
            $this->amount += $product->quantity * $product->price;
            $this->amountWithDiscount += $product->quantity * ($discountCode ? $product->priceWithDiscount : $product->price);
            $product->priceWithDiscount = $discountCode ? $product->priceWithDiscount : $product->price;
            $this->products[$key] = $product;

            if ($product->hasStroika) {
                $this->hasStroika = true;
            }
        }

        $this->products = $products;
        $this->discountCode = $discountCode;
        $this->geoPointId = $geoPointId;
    }
}
