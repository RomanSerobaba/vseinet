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
     * @VIA\Description("Общая стоимость доставки товаров до регионального склада")
     */
    public $deliveryTaxAmount = 0;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Стоимость доставки товаров до точки получения")
     */
    public $deliveryToRepresentativeTaxAmount = 0;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Стоимость курьерской доставки до подъезда")
     */
    public $deliveryCharges = 0;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Общая стоимость доставки товаров от подъезда до квартиры")
     */
    public $liftingCharges = 0;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Стоимость доставки товаров до транспортной компании")
     */
    public $transportCompanyDeliveryCharges = 0;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Общая сумма товаров со скидкой")
     */
    public $amountWithDiscount = 0;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Общая сумма товаров со скидкой")
     */
    public $paymentTypeComissionPercent = 0;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Размер комиссии за выбранный тип оплаты")
     */
    public $paymentTypeComissionAmount = 0;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Итого к оплате")
     */
    public $summary = 0;

    /**
     * @Assert\Type(type="string")
     */
    public $discountCode;

    /**
     * @Assert\Type(type="array<AppBundle\Bus\Cart\Query\DTO\Product>")
     */
    public $products;


    public function __construct(array $products, int $deliveryCharges, int $liftingCharges, int $transportCompanyDeliveryCharges, int $deliveryToRepresentativeTaxAmount, float $paymentTypeComissionPercent, ?string $discountCode)
    {
        foreach ($products as $product) {
            $this->total += $product->quantity;
            $this->amount += $product->quantity * $product->price;
            $this->amountWithDiscount += $product->quantity * $product->priceWithDiscount;
            $this->deliveryTaxAmount += $product->quantity * $product->deliveryTax;
            $this->products[$product->id] = $product;

            if ($product->hasStroika) {
                $this->hasStroika = true;
            }
        }

        $this->amountWithDiscount = round($this->amountWithDiscount, -2);
        $this->deliveryCharges = $deliveryCharges;
        $this->liftingCharges = $liftingCharges;
        $this->transportCompanyDeliveryCharges = $transportCompanyDeliveryCharges;
        $this->deliveryToRepresentativeTaxAmount = $deliveryToRepresentativeTaxAmount;
        $this->discountCode = $discountCode;
        $this->summary = $this->amountWithDiscount + $this->deliveryTaxAmount + $this->deliveryCharges + $this->liftingCharges;
        $this->paymentTypeComissionAmount = round($this->summary * $paymentTypeComissionPercent / 100);
        $this->summary += $this->paymentTypeComissionAmount;
    }
}
