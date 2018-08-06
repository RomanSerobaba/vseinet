<?php 

namespace ShopBundle\Bus\Cart\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CartProduct
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $price;

    /**
     * @Assert\Type(type="string")
     */
    public $brandLogo;

    /**
     * @Assert\Type(type="integer")
     */
    public $brandId;

    /**
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @Assert\Type(type="string")
     */
    public $geoPointName;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $changeTypeId;
}