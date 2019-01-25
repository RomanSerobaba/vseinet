<?php

namespace AppBundle\Bus\Cart\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation AS VIA;

class CartSummary
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
     * @Enum("AppBundle\Enum\PaymentTypeCode")
     * @VIA\Description("Код способа оплаты")
     */
    public $paymentTypeCode;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Способ оплаты")
     */
    public $paymentTypeName;

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


    public function __construct(array $products, string $discountCode = NULL, int $deliveryCharges = NULL, int $liftingFloor, int $transportCompanyDeliveryCharges = NULL, float $paymentTypeComissionPercent = NULL, string $paymentTypeCode = NULL, string $paymentTypeName = NULL)
    {
        foreach ($products as $product) {
            $this->total += $product->quantity;
            $this->amount += $product->quantity * $product->price;
            $this->amountWithDiscount += $product->quantity * $product->priceWithDiscount;
            $this->deliveryTaxAmount += $product->quantity * $product->deliveryTax;
            $this->deliveryToRepresentativeTaxAmount += $product->quantity * $product->regionDeliveryTax;
            $this->liftingCharges += $product->quantity * $liftingFloor * $product->liftingCost;
            $this->products[$product->id] = $product;

            if ($product->hasStroika) {
                $this->hasStroika = true;
            }
        }

        $this->paymentTypeCode = $paymentTypeCode;
        $this->paymentTypeName = $paymentTypeName;
        $this->deliveryCharges = $deliveryCharges;
        $this->transportCompanyDeliveryCharges = $transportCompanyDeliveryCharges;
        $this->discountCode = $discountCode;
        $this->paymentTypeComissionPercent = $paymentTypeComissionPercent;
        $this->summary = $this->amountWithDiscount + $this->deliveryTaxAmount + $this->liftingCharges + $this->deliveryCharges + $this->transportCompanyDeliveryCharges + $this->deliveryToRepresentativeTaxAmount;
        $this->paymentTypeComissionAmount = round($this->summary * $paymentTypeComissionPercent / 100);
        $this->summary += $this->paymentTypeComissionAmount;
    }
}
