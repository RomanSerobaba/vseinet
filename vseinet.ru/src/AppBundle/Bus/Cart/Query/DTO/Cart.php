<?php

namespace AppBundle\Bus\Cart\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation AS VIA;

class Cart
{
    /**
     * @Assert\Type(type="integer", message="Количество товаров должно быть числом")
     * @VIA\Description("Количество товаров")
     */
    public $total = 0;

    /**
     * @Assert\Type(type="integer", message="Общая сумма товаров должна быть числом")
     * @VIA\Description("Общая сумма товаров")
     */
    public $amount = 0;

    /**
     * @Assert\Type(type="bool")
     * @VIA\Description("Признак того, есть ли среди товаров строительные материалы")
     */
    public $hasStroika = false;

    /**
     * @Assert\Type(type="integer", message="Общая сумма товаров со скидкой должна быть числом")
     * @VIA\Description("Общая сумма товаров со скидкой")
     */
    public $amountWithDiscount = 0;

    /**
     * @Assert\Type(type="AppBundle\Entity\DiscountCode")
     */
    public $discountCode;

    /**
     * @Assert\Type(type="integer", message="Ид кода скидки должен быть числом")
     * @VIA\Description("Ид кода скидки")
     */
    public $discountCodeId;

    /**
     * @Assert\Type(type="integer", message="Ид точки должен быть числом")
     * @VIA\Description("Ид точки")
     */
    public $geoPointId;

    /**
     * @Assert\All(
     *  @Assert\Type(type="AppBundle\Bus\Cart\Query\DTO\Product")
     * )
     */
    public $products;


    public function __construct(array $products, string $discountCode, int $discountCodeId = NULL, int $geoPointId = NULL)
    {
        foreach ($products as $key => $product) {
            $this->total += $product->quantity;
            $this->amount += $product->quantity * $product->price;
            $this->amountWithDiscount += $product->quantity * ($discountCodeId ? $product->priceWithDiscount : $product->price);
            $product->priceWithDiscount = $discountCodeId ? $product->priceWithDiscount : $product->price;
            $this->products[$key] = $product;

            if ($product->hasStroika) {
                $this->hasStroika = true;
            }
        }

        $this->products = $products;
        $this->discountCode = $discountCode;
        $this->discountCodeId = $discountCodeId;
        $this->geoPointId = $geoPointId;
    }
}
