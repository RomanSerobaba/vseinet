<?php 

namespace ShopBundle\Bus\Cart\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Cart
{
    /**
     * @Assert\Type(type="integer")
     */
    public $count;

    /**
     * @Assert\Type(type="integer")
     */
    public $discount;

    /**
     * @Assert\Type(type="string")
     */
    public $discountCode;

    /**
     * @Assert\Type(type="integer")
     */
    public $brandId;

    /**
     * @Assert\Type(type="integer")
     */
    public $total;

    /**
     * @Assert\Type(type="integer")
     */
    public $totalDiscount;

    /**
     * @Assert\Type(type="integer")
     */
    public $delivery2City;

    /**
     * @Assert\Type(type="integer")
     */
    public $rise;

    /**
     * @Assert\Type(type="array<ShopBundle\Bus\Cart\Query\DTO\CartProduct>")
     */
    public $products;
}