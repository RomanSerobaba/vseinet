<?php

namespace AppBundle\Bus\Cart\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as VIC;

class Product
{
    /**
     * @Assert\Type(type="integer", message="Ид должен быть числом")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer", message="Ид категории должен быть числом")
     */
    public $categoryId;

    /**
     * @Assert\Type(type="integer", message="Минимальное количество заказа должно быть числом")
     */
    public $minQuantity;

    /**
     * @Assert\Type(type="string")
     */
    public $baseSrc;

    /**
     * @Assert\Type(type="integer", message="Цена должна быть числом")
     */
    public $price;

    /**
     * @VIC\Enum("AppBundle\Enum\ProductAvailabilityCode")
     */
    public $availabilityCode;

    /**
     * @Assert\Type(type="integer", message="Стоимость доставки до центрального склада должна быть числом")
     */
    public $deliveryTax;

    /**
     * @Assert\Type(type="integer", message="Стоимость подъема за этаж должна быть числом")
     */
    public $liftingCost;

    /**
     * @Assert\Type(type="integer", message="Стоимость доставки до представительства должна быть числом")
     */
    public $regionDeliveryTax;

    /**
     * @Assert\Type(type="integer", message="Количество должно быть числом")
     */
    public $quantity;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasStroika;

    /**
     * @Assert\Type(type="integer", message="Цена со скидкой должна быть числом")
     */
    public $priceWithDiscount;

    /**
     * @Assert\Type(type="integer", message="Количество в наличии должно быть числом")
     */
    public $reserveQuantity;

    /**
     * @Assert\Type(type="integer", message="Ценник в магазине должна быть числом")
     */
    public $storePricetag;

    /**
     * @Assert\Type(type="integer", message="Размер скидки")
     */
    public $discountAmount;


    public function __construct($id, $name, $categoryId, $minQuantity, $baseSrc, $price, $availabilityCode, $deliveryTax, $liftingCost, $quantity, $hasStroika, $discountAmount, $reserveQuantity, $storePricetag)
    {
        $this->id = $id;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->minQuantity = $minQuantity;
        $this->baseSrc = $baseSrc;
        $this->price = $price;
        $this->availabilityCode = $availabilityCode;
        $this->quantity = $quantity;
        $this->deliveryTax = $deliveryTax;
        $this->regionDeliveryTax = 0;
        $this->liftingCost = $liftingCost;
        $this->hasStroika = (bool) $hasStroika;
        $this->priceWithDiscount = (int) round($price - $discountAmount, -2);
        $this->discountAmount = $discountAmount;
        $this->reserveQuantity = (int) $reserveQuantity;
        $this->storePricetag = $storePricetag;
    }
}
