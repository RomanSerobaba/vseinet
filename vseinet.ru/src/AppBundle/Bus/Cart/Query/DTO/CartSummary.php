<?php

namespace AppBundle\Bus\Cart\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CartSummary
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
     * @Assert\Type(type="integer", message="Общая стоимость доставки товаров до регионального склада должна быть числом")
     * @VIA\Description("Общая стоимость доставки товаров до регионального склада")
     */
    public $deliveryTaxAmount = 0;

    /**
     * @Assert\Type(type="integer", message="Стоимость доставки товаров до точки получения должна быть числом")
     * @VIA\Description("Стоимость доставки товаров до точки получения")
     */
    public $deliveryToRepresentativeTaxAmount = 0;

    /**
     * @Assert\Type(type="integer", message="Стоимость курьерской доставки до подъезда должна быть числом")
     * @VIA\Description("Стоимость курьерской доставки до подъезда")
     */
    public $deliveryCharges = 0;

    /**
     * @Assert\Type(type="integer", message="Общая стоимость доставки товаров от подъезда до квартиры должна быть числом")
     * @VIA\Description("Общая стоимость доставки товаров от подъезда до квартиры")
     */
    public $liftingCharges = 0;

    /**
     * @Assert\Type(type="integer", message="Стоимость доставки товаров до транспортной компании должна быть числом")
     * @VIA\Description("Стоимость доставки товаров до транспортной компании")
     */
    public $transportCompanyDeliveryCharges = 0;

    /**
     * @Assert\Type(type="integer", message="Стоимость доставки товаров почтой должна быть числом")
     * @VIA\Description("Стоимость доставки товаров почтой")
     */
    public $postDeliveryCharges = 0;

    /**
     * @Assert\Type(type="integer", message="Общая сумма товаров со скидкой должна быть числом")
     * @VIA\Description("Общая сумма товаров со скидкой")
     */
    public $amountWithDiscount = 0;

    /**
     * @Assert\Type(type="integer", message="Общая сумма товаров со скидкой должна быть числом")
     * @VIA\Description("Общая сумма товаров со скидкой")
     */
    public $paymentTypeComissionPercent = 0;

    /**
     * @Assert\Type(type="integer", message="Размер комиссии за выбранный тип оплаты должен быть числом")
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
     * @Assert\Type(type="integer", message="Итого к оплате должно быть числом")
     * @VIA\Description("Итого к оплате")
     */
    public $summary = 0;

    /**
     * @Assert\Type(type="string")
     */
    public $discountCode;

    /**
     * @Assert\Type(type="integer", message="Ид кода скидки должен быть числом")
     * @VIA\Description("Ид кода скидки")
     */
    public $discountCodeId;

    /**
     * @Assert\Type(type="array<AppBundle\Bus\Cart\Query\DTO\Product>")
     */
    public $products;

    public function __construct(array $products, string $discountCode, int $discountCodeId = null, int $deliveryCharges = null, int $liftingFloor, int $transportCompanyDeliveryCharges = null, $postDeliveryCharges = null, float $paymentTypeComissionPercent = null, string $paymentTypeCode = null, string $paymentTypeName = null)
    {
        foreach ($products as $product) {
            $this->total += $product->quantity;
            $this->amount += $product->quantity * ($product->storePricetag ?? $product->price);
            $this->amountWithDiscount += $product->quantity * (($product->storePricetag ?? $product->price) - ($discountCode ? $product->discountAmount : 0));
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
        $this->postDeliveryCharges = $postDeliveryCharges;
        $this->discountCode = $discountCode;
        $this->discountCodeId = $discountCodeId;
        $this->paymentTypeComissionPercent = $paymentTypeComissionPercent;
        $this->summary = $this->amountWithDiscount + $this->deliveryTaxAmount + $this->liftingCharges + $this->deliveryCharges + $this->transportCompanyDeliveryCharges + $this->deliveryToRepresentativeTaxAmount;
        $this->paymentTypeComissionAmount = round($this->summary * $paymentTypeComissionPercent / 100, -2);
        $this->summary += $this->paymentTypeComissionAmount;
    }
}
